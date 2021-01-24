<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek378 extends AbstractMyCdek
{
  protected $tariff_code = 378;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_pvz_postamat_378";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Экономичная посылка склад-постамат";
    $this->method_description =
      "Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.";
    $this->title = "ТК СДЭК Экономичная посылка склад-постамат";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
