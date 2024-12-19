<?php declare(strict_types=1);
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Config;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Validation\ValidationException;

class P12Certificate extends \Magento\Config\Model\Config\Backend\Encrypted
{
    /**
     * @var \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface
     */
    protected $requestData;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var int max KB
     */
    protected $maxFileSize = 50;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $encryptor,
            $resource,
            $resourceCollection,
            $data
        );

        $this->requestData = $requestData;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $file  = $this->getFileData();

        if (!empty($file)) {
            if (strripos($file['name'], '.p12') !== strlen($file['name']) - 4) {
                throw new ValidationException(__('CyberSource certificate file must be *.p12'));
            }

            $file['uploaded'] = date('c');
            $file['contents'] = base64_encode(file_get_contents($file['tmp_name']));

            $this->setValue(json_encode($file));
        } elseif (is_array($value) && isset($value['delete'])) {
            // Handle delete flag
            $this->setValue('');
        } else {
            // No file uploaded; mark it as obscured so it's left unchanged.
            $this->setValue('******');
        }

        parent::beforeSave();
    }

    /**
     * Receiving uploaded file data
     *
     * @return array
     * @since 100.1.0
     */
    protected function getFileData()
    {
        $file = [];
        $value = $this->getValue();
        $tmpName = $this->requestData->getTmpName($this->getPath());
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->requestData->getName($this->getPath());
        } elseif (!empty($value['tmp_name'])) {
            $file['tmp_name'] = $value['tmp_name'];
            $file['name'] = isset($value['value']) ? $value['value'] : $value['name'];
        }

        return $file;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return void
     * @throws LocalizedException
     */
    protected function validateMaxSize($filePath)
    {
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $fileStat  = $directory->stat($directory->getRelativePath($filePath));

        if ($fileStat['size'] > $this->maxFileSize * 1024) {
            throw new LocalizedException(
                __('The file you\'re uploading exceeds the limit of %1 kilobytes.', $this->maxFileSize)
            );
        }
    }
}
