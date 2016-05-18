<?php

namespace Evalfor\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvalforUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
