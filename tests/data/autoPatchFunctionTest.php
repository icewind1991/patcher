<?php

namespace Auto\Test;

if (!function_exists('\Auto\Test\test')) {
	function test() {
		return sleep(0);
	}
}

return function () {
	return sleep(0);
};
