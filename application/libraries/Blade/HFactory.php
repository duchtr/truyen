<?php 
require_once"HView.php";
use Illuminate\View\Factory;
class HFactory extends Factory{
	public function make($view, $data = [], $mergeData = [])
    {
        if (isset($this->aliases[$view])) {
            $view = $this->aliases[$view];
        }

        $view = $this->normalizeName($view);
        $path = $this->finder->find($view);
        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new HView($this, $this->getEngineFromPath($path), $view, $path, $data));

        return $view;
    }
}
 ?>
