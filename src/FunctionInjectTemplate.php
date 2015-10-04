namespace __NAMESPACE__ {
	function __METHOD__() {
		/** @var \Icewind\Patcher\Injector $injector */
		$injector = $GLOBALS['__INJECTORID__'];
		return $injector->handleCall('__METHOD__', func_get_args());
	}
}
