<?php

/**
 * Class SentryErrorLogger подключет логирование PHP ошибок в облако, для инициализации достаточно вызвать
 * конструктор класса с указанием названия бибилотеки.
 */
class SentryErrorLogger {

	protected $dsn; // Идентификатор проекта в Sentry
    protected $libraryName; // название библиотеки для тегирования ошибок, например pbxcore, web-interface и т.д.
    protected $licKey; // текущий лицензионный ключ
    protected $companyName; // Компания
    protected $email; // Компания
    protected $release; // Релиз АТС
    protected $environment; // development или production, подменяется в teamcity
    protected $enabled; // разрешил ли пользователь логирование ошибок в облако


	function __construct($libraryName) {
		$this->dsn = 'https://a8d729459beb446eb3cbb9df997dcc7b@centry.miko.ru/1';
		$this->libraryName = $libraryName;
        $this->environment = 'production';
		if (file_exists('/tmp/licenseInfo')) {
            $licenseInfo = json_decode(file_get_contents('/tmp/licenseInfo', FALSE));
			$this->licKey =  $licenseInfo->{'@attributes'}->key;
			$this->email =  $licenseInfo->{'@attributes'}->email;
            $this->companyName = $licenseInfo->{'@attributes'}->companyname;
		}
		if (file_exists('/etc/version')) {
			$pbxVersion = str_replace("\n","",file_get_contents('/etc/version', FALSE));
			$this->release = "mikopbx@{$pbxVersion}";
		}
		$this->enabled = file_exists('/tmp/sendmetrics');
	}

	/**
	 * Если в настройках PBX разрешено отправлять сообщения об ошибках на сервер,
	 * то функция инициализирует подпистему облачного логирования ошибок Sentry
	 *
	 * @return Boolean - результат инициализации
	 */
	public function init(){
		if ($this->enabled){
			Sentry\init([
				'dsn'     => $this->dsn,
				'release' => $this->release,
                'environment' => $this->environment,
			]);
			Sentry\configureScope(function (Sentry\State\Scope $scope): void {
				if (isset($this->email)) {
					$scope->setUser(['id' => $this->email]);
				}
                if (isset($this->licKey)) {
                    $scope->setExtra('key', $this->licKey);
                }
                if (isset($this->companyName)) {
                    $scope->setExtra('company', $this->companyName);
                }
				if (isset($this->libraryName)) {
					$scope->setTag('library', $this->libraryName);
				}
			});
		}
		return $this->enabled;
	}

	/**
	 * Обрабатывает ошибку и отправляет ее в Sentry
	 *
	 * @param \Exception $e
	 */
	public function captureException (Exception $e ):void{
		Sentry\captureException($e);
	}
}

