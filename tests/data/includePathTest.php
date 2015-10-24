<?php

namespace Auto\Test\Path;

if (!function_exists('\Auto\Test\Path\test')) {
	function test() {
		return sleep(0);
	}
}

return function () {
	return sleep(0);
};
