<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2012 Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://www.isotopeecommerce.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Class ModuleIsotopeProductReader
 *
 * Front end module Isotope "product reader".
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 */
class ModuleIsotopeProductReader extends ModuleIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_productreader';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ISOTOPE ECOMMERCE: PRODUCT READER ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Return if no product has been specified
		if ($this->Input->get('product') == '')
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate AJAX scripts
	 * @return string
	 */
	public function generateAjax()
	{
		$objProduct = IsotopeFrontend::getProduct($this->Input->get('product'), IsotopeFrontend::getReaderPageId(), false);

		if ($objProduct)
		{
			return $objProduct->generateAjax($this);
		}

		return '';
	}


	/**
	 * Generate module
	 * @return void
	 */
	protected function compile()
	{
		global $objPage;

		$objProduct = IsotopeFrontend::getProductByAlias($this->Input->get('product'), IsotopeFrontend::getReaderPageId());

		if (!$objProduct)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			$this->Template = new FrontendTemplate('mod_message');
			$this->Template->type = 'empty';
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['invalidProductInformation'];
			return;
		}

		$this->Template->product = $objProduct->generate((strlen($this->iso_reader_layout) ? $this->iso_reader_layout : $objProduct->reader_template), $this);
		$this->Template->product_id = ($objProduct->cssID[0] != '') ? ' id="' . $objProduct->cssID[0] . '"' : '';
		$this->Template->product_class = trim('product ' . $objProduct->cssID[1]);
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

		$objPage->pageTitle = strip_insert_tags($objProduct->name);
		$objPage->description = $this->prepareMetaDescription($objProduct->description_meta);

		$GLOBALS['TL_KEYWORDS'] .= (strlen($GLOBALS['TL_KEYWORDS']) ? ', ' : '') . $objProduct->keywords_meta;
	}
}
