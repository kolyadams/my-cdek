<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek15 extends AbstractMyCdek
{
  protected $tariff_code = 15;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_pvz_15";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Экспресс тяжеловесы склад-склад";
    $this->method_description = "Классическая экспресс-доставка по России грузов.";
    $this->title = "ТК СДЭК Экспресс тяжеловесы склад-склад";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
