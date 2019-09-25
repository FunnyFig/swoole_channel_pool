<?php

namespace FunnyFig\Swoole;

use chan;

class ChannelPool {
	static $instance;

	static function inst(int $n=0) {
		if ($n) {
			if (self::$instance !== null) {
				throw new \RuntimeException("ChannelPool is already instantiated");
			}

			$self_class = self::class;
			self::$instance = new $self_class($n);
		}

		if (self::$instance === null) {
			throw new \RuntimeException("ChannelPool is not yet instantiated");
		}

		return self::$instance;
	}

	protected $pool;

	protected function __construct(int $n=8)
	{
		if ($n<1) {
			throw new InvalidArgumentException();
		}
		$this->pool = new chan($n);
		for ($i=0; $i<$n; ++$i) {
			$this->put(new chan(1));
		}
	}

	function get() : Swoole\Coroutine\Channel
	{
		return $this->pool->pop();
	}

	function put(Swoole\Coroutine\Channel $chan)
	{
		return $this->pool->push($chan);
	}

	function close()
	{
		$this->pool->close();
		$this->pool = null;
	}
}

