<?php


require_once 'ParticipationParser.php';

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTwigParser implements ParticipationParser{

	/**
	 * @var
	 */
	protected $result_startexam;
	/**
	 * @var
	 */
	protected $result_learnmodule;
	/**
	 * @var
	 * @return bool
	 */
	protected $participation_videoconference;
	/**
	 * @var
	 */
	protected $result_recess;
	/**
	 * @var
	 */
	protected $teacher_FullName;
	/**
	 * @var
	 */
	protected $teacher_Function;
	/**
	 * @var
	 */
	protected $user_FullName;
	/**
	 * @var
	 */
	protected $questions_count;
	/**
	 * @var
	 */
	protected $theme_get;
	/**
	 * @var
	 */
	protected $modules_done;
	/**
	 * @var
	 */
	protected $conferences_participated;
	/**
	 * @var
	 */
	protected $homework_done;



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
		global $result_startexam, $result_learnmodule, $participation_videoconference, $result_recess,
			$teacher_FullName, $teacher_Function, $user_FullName, $questions_count, $theme_get, $modules_done,
			$conferences_participated, $homework_done;

		$this->result_startexam = $result_startexam;
		$this->result_learnmodule = $result_learnmodule;
		$this->participation_videoconference = $participation_videoconference;
		$this->result_recess = $result_recess;
		$this->teacher_FullName = $teacher_FullName;
		$this->teacher_Function = $teacher_Function;
		$this->user_FullName = $user_FullName;
		$this->questions_count = $questions_count;
		$this->theme_get = $theme_get;
		$this->modules_done = $modules_done;
		$this->conferences_participated = $conferences_participated;
		$this->homework_done = $homework_done;


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
		global $template;
		$loader = new \Twig_Loader_Filesystem('./Templates');
		$twig = new \Twig_Environment($loader, $this->options);

		//loads template for twig
		$template = $twig->load('./Templates/Teilnahmebescheinigung.html');

		//TODO Fill Placeholders with Name and etc.
		echo $template->render(array(' result.startexam ' => '$this->result_startexam' , 'result.learnmodule' => '', 'participation_videoconference' => '', 'result.recess' => '',
			'teacher.getFullname' => '', 'teacher.getFunction' => '', 'user.getFullname' => '', 'questions.getCount' => '', 'theme.get' => '',
			'modules.done' => '', ' conferences.participated ' => '', ' homework.done ' => '',));
		
		return $template->render(array(' result.startexam ' => '', 'result.learnmodule' => '', 'participation_videoconference' => '', 'result.recess' => '',
			'teacher.getFullname' => '', 'teacher.getFunction' => '', 'user.getFullname' => '', 'questions.getCount' => '', 'theme.get' => '',
			'modules.done' => '', ' conferences.participated ' => '', ' homework.done ' => '',));
		//return $twig->render($text, $replacements);



	}
}