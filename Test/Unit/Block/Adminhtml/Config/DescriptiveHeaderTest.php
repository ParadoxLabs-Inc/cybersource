<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Block\Adminhtml\Config;

use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use ParadoxLabs\CyberSource\Block\Adminhtml\Config\DescriptiveHeader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Block\Adminhtml\Config\DescriptiveHeader
 */
class DescriptiveHeaderTest extends TestCase
{
    private DescriptiveHeader $block;
    private Context|MockObject $contextMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);

        $this->block = new DescriptiveHeader($this->contextMock, []);
    }

    /**
     * Build an element stub exposing the three values the header renders.
     */
    private function buildElement(string $htmlId, string $label, ?string $comment): AbstractElement&MockObject
    {
        // getHtmlId is declared on AbstractElement; getLabel/getComment are DataObject magic.
        $element = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHtmlId'])
            ->addMethods(['getLabel', 'getComment'])
            ->getMockForAbstractClass();

        $element->method('getHtmlId')->willReturn($htmlId);
        $element->method('getLabel')->willReturn($label);
        $element->method('getComment')->willReturn($comment);

        return $element;
    }

    public function testRenderProducesSubHeadRowWithLabelAndComment(): void
    {
        $element = $this->buildElement('pdl_cybs_uc', 'Unified Checkout', 'Configure the UC drop-in.');

        $html = $this->block->render($element);

        $this->assertStringContainsString('<tr class="system-fieldset-sub-head" id="row_pdl_cybs_uc">', $html);
        $this->assertStringContainsString('<td colspan="5">', $html);
        $this->assertStringContainsString('<h4 id="pdl_cybs_uc">Unified Checkout</h4>', $html);
        $this->assertStringContainsString('Configure the UC drop-in.', $html);
        $this->assertStringEndsWith('</td></tr>', $html);
    }

    /**
     * The row id and the heading anchor id must both derive from the element html id, so the admin
     * config JS (which toggles rows by `row_<id>`) can find and hide the header with its fields.
     */
    public function testRenderUsesElementHtmlIdForBothRowAndHeadingAnchor(): void
    {
        $element = $this->buildElement('my_section_field', 'Label', 'Comment');

        $html = $this->block->render($element);

        $this->assertStringContainsString('id="row_my_section_field"', $html);
        $this->assertStringContainsString('id="my_section_field"', $html);
    }

    /**
     * The comment paragraph is the whole point of this block over the stock Heading; it must be
     * emitted as an indented `.comment` paragraph.
     */
    public function testRenderEmitsCommentAsIndentedCommentParagraph(): void
    {
        $element = $this->buildElement('id1', 'Label', 'Some descriptive text.');

        $html = $this->block->render($element);

        $this->assertStringContainsString(
            '<p class="comment" style="padding-left:2.8rem">Some descriptive text.</p>',
            $html
        );
    }

    /**
     * Comments are authored in system.xml and may legitimately carry markup (links to docs); the
     * block must pass them through rather than escaping them into literal text.
     */
    public function testRenderPassesThroughHtmlMarkupInComment(): void
    {
        $element = $this->buildElement('id1', 'Label', 'See <a href="https://example.com">the docs</a>.');

        $html = $this->block->render($element);

        $this->assertStringContainsString('<a href="https://example.com">the docs</a>', $html);
    }

    /**
     * A field with no comment is valid config; it must still render a well-formed row rather than
     * erroring on the null.
     */
    public function testRenderWithNullCommentStillRendersWellFormedRow(): void
    {
        $element = $this->buildElement('id1', 'Label', null);

        $html = $this->block->render($element);

        $this->assertStringContainsString('<h4 id="id1">Label</h4>', $html);
        $this->assertStringContainsString('<p class="comment" style="padding-left:2.8rem"></p>', $html);
        $this->assertStringEndsWith('</td></tr>', $html);
    }

    public function testRenderReturnsString(): void
    {
        $element = $this->buildElement('id1', 'Label', 'Comment');

        $this->assertIsString($this->block->render($element));
    }
}
