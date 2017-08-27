<?php

namespace Box\Spout\Reader\ODS\Creator;

use Box\Spout\Common\Helper\GlobalFunctionsHelper;
use Box\Spout\Common\Manager\OptionsManagerInterface;
use Box\Spout\Reader\Common\Creator\EntityFactoryInterface;
use Box\Spout\Reader\Common\Entity\Options;
use Box\Spout\Reader\Common\XMLProcessor;
use Box\Spout\Reader\ODS\RowIterator;
use Box\Spout\Reader\ODS\Sheet;
use Box\Spout\Reader\ODS\SheetIterator;
use Box\Spout\Reader\Wrapper\XMLReader;

/**
 * Class EntityFactory
 * Factory to create entities
 *
 * @package Box\Spout\Reader\ODS\Creator
 */
class EntityFactory implements EntityFactoryInterface
{
    /** @var HelperFactory */
    private $helperFactory;

    /**
     * @param HelperFactory $helperFactory
     */
    public function __construct(HelperFactory $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    /**
     * @param string $filePath Path of the file to be read
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @return SheetIterator
     */
    public function createSheetIterator($filePath, $optionsManager)
    {
        $escaper = $this->helperFactory->createStringsEscaper();
        $settingsHelper = $this->helperFactory->createSettingsHelper($this);

        return new SheetIterator($filePath, $optionsManager, $escaper, $settingsHelper, $this);
    }

    /**
     * @param XMLReader $xmlReader XML Reader
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $sheetName Name of the sheet
     * @param bool $isSheetActive Whether the sheet was defined as active
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @return Sheet
     */
    public function createSheet($xmlReader, $sheetIndex, $sheetName, $isSheetActive, $optionsManager)
    {
        $rowIterator = $this->createRowIterator($xmlReader, $optionsManager);

        return new Sheet($rowIterator, $sheetIndex, $sheetName, $isSheetActive);
    }

    /**
     * @param XMLReader $xmlReader XML Reader
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @return RowIterator
     */
    private function createRowIterator($xmlReader, $optionsManager)
    {
        $shouldFormatDates = $optionsManager->getOption(Options::SHOULD_FORMAT_DATES);
        $cellValueFormatter = $this->helperFactory->createCellValueFormatter($shouldFormatDates);
        $xmlProcessor = $this->createXMLProcessor($xmlReader);

        return new RowIterator($xmlReader, $optionsManager, $cellValueFormatter, $xmlProcessor);
    }

    /**
     * @return XMLReader
     */
    public function createXMLReader()
    {
        return new XMLReader();
    }

    /**
     * @param $xmlReader
     * @return XMLProcessor
     */
    private function createXMLProcessor($xmlReader)
    {
        return new XMLProcessor($xmlReader);
    }

    /**
     * @return \ZipArchive
     */
    public function createZipArchive()
    {
        return new \ZipArchive();
    }
}
