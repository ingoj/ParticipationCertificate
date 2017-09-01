<?php


require_once 'ParticipationParser.php';

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTwigParser implements ParticipationParser{

	/**
	 * @var array
	 */
	protected $options = array(
		'autoescape' => false,
	);

	/**
	 * ilParticipationCertificateTwigParser constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = array()) {

		$this->options = array_merge($this->options, $options);
		$this->loadTwig();
	}


	/**
	 * Bootstrap twig engine
	 */
	protected function loadTwig(){
		static $loaded = false;
		if (!$loaded) {
			require_once (dirname(dirname(dirname(__FILE__))) . 'vendor/twig/twig/lib/Twig/Autoloader.php');
			Twig_Autoloader::register();
			$loaded = true;
		}
	}

	/**
	 * @param string $text
	 * @param array  $replacements
	 * @return string
	 */
	public function parse($text, array $replacements = array()) {
		$loader = new \Twig_Loader_String();
		$twig = new \Twig_Environment($loader, $this->options);

		return $twig->render($text, $replacements);



	}
}