<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation;


/**
 * Translation using Zend\I18n\Translator\Translator
 *
 * @package MW
 * @subpackage Translation
 */
class Zend2
	extends \Aimeos\MW\Translation\Base
	implements \Aimeos\MW\Translation\Iface
{
	private $adapter;
	private $options;
	private $translationSources;
	private $translations = [];


	/**
	 * Initializes the translation object using Zend_Translate.
	 * This implementation only accepts files as source for the Zend_Translate_Adapter.
	 *
	 * @param array $translationSources Associative list of translation domains and lists of translation directories.
	 * 	Translations from the first file aren't overwritten by the later ones
	 * as key and the directory where the translation files are located as value.
	 * @param string $adapter Name of the Zend translation adapter
	 * @param string $locale ISO language name, like "en" or "en_US"
	 * @param string $options Associative array containing additional options for Zend\I18n\Translator\Translator
	 *
	 * @link http://framework.zend.com/manual/2.3/en/modules/zend.i18n.translating.html
	 */
	public function __construct( array $translationSources, $adapter, $locale, array $options = [] )
	{
		parent::__construct( $locale );

		$this->adapter = $adapter;
		$this->options = $options;
		$this->options['locale'] = (string) $locale;
		$this->translationSources = $translationSources;
	}


	/**
	 * Returns the translated string for the given domain.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String to be translated
	 * @return string The translated string
	 * @throws \Aimeos\MW\Translation\Exception Throws exception on initialization of the translation
	 */
	public function dt( $domain, $singular )
	{
		$singular = (string) $singular;

		try
		{
			$locale = $this->getLocale();

			foreach( $this->getTranslations( $domain ) as $object )
			{
				if( ( $string = $object->translate( $singular, $domain, $locale ) ) != $singular ) {
					return $string;
				}
			}
		}
		catch( \Exception $e ) { ; } // Discard errors, return original string instead

		return (string) $singular;
	}


	/**
	 * Returns the translated singular or plural form of the string depending on the given number.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to choose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number
	 * @throws \Aimeos\MW\Translation\Exception Throws exception on initialization of the translation
	 *
	 * @link http://framework.zend.com/manual/en/zend.translate.plurals.html
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		$singular = (string) $singular;
		$plural = (string) $plural;
		$number = (int) $number;

		try
		{
			$locale = $this->getLocale();

			foreach( $this->getTranslations( $domain ) as $object )
			{
				if( ( $string = $object->translatePlural( $singular, $plural, $number, $domain, $locale ) ) != $singular ) {
					return $string;
				}
			}
		}
		catch( \Exception $e ) { ; } // Discard errors, return original string instead

		if( $this->getPluralIndex( $number, $this->getLocale() ) > 0 ) {
			return (string) $plural;
		}

		return (string) $singular;
	}


	/**
	 * Returns all locale string of the given domain.
	 *
	 * @param string $domain Translation domain
	 * @return array Associative list with original string as key and associative list with index => translation as value
	 */
	public function getAll( $domain )
	{
		$messages = [];
		$locale = $this->getLocale();

		foreach( $this->getTranslations( $domain ) as $object ) {
			$messages = $messages + (array) $object->getMessages( $domain, $locale );
		}

		return $messages;
	}


	/**
	 * Returns the initialized Zend translation object which contains the translations.
	 *
	 * @param string $domain Translation domain
	 * @return array List of translation objects implementing Zend_Translate
	 * @throws \Aimeos\MW\Translation\Exception If initialization fails
	 */
	protected function getTranslations( $domain )
	{
		if( !isset( $this->translations[$domain] ) )
		{
			if ( !isset( $this->translationSources[$domain] ) )
			{
				$msg = sprintf( 'No translation directory for domain "%1$s" available', $domain );
				throw new \Aimeos\MW\Translation\Exception( $msg );
			}

			$locale = $this->getLocale();
			// Reverse locations so the former gets not overwritten by the later
			$locations = array_reverse( $this->getTranslationFileLocations( $this->translationSources[$domain], $locale ) );

			foreach( $locations as $location )
			{
				$translator = \Zend\I18n\Translator\MwTranslator::factory( $this->options );
				$translator->addTranslationFile( $this->adapter, $location, $domain, $locale );

				$this->translations[$domain][$location] = $translator;
			}
		}

		return ( isset( $this->translations[$domain] ) ? $this->translations[$domain] : [] );
	}

}
