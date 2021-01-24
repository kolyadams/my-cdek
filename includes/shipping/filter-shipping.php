<?php

add_filter(
  "woocommerce_package_rates",
  function ($rates, $package) {
    //убираем классику если она пуста, показываем если не пуста
    if (isset($rates["mycdek_pvz_136"]) && empty($rates["mycdek_pvz_136"]->get_cost())) {
      unset($rates["mycdek_pvz_136"]); //склад-склад
    }

    if (isset($rates["mycdek_137"]) && empty($rates["mycdek_137"]->get_cost())) {
      unset($rates["mycdek_137"]); //склад-дверь
    }

    if (isset($rates["mycdek_pvz_postamat_368"]) && empty($rates["mycdek_pvz_postamat_368"]->get_cost())) {
      unset($rates["mycdek_pvz_postamat_368"]); //склад-дверь
    }

    //убираем классическую доставку для ИМ если доступна экономическая доставка для ИМ, а если экономическая пуста, то убираем ее
    if (isset($rates["mycdek_233"]) && empty($rates["mycdek_233"]->get_cost())) {
      unset($rates["mycdek_233"]); //экономичный склад-дверь им
    } elseif(isset($rates["mycdek_233"]) && isset($rates["mycdek_137"])) {
      unset($rates["mycdek_137"]); //склад-дверь им
    }

    if (isset($rates["mycdek_pvz_234"]) && empty($rates["mycdek_pvz_234"]->get_cost())) {
      unset($rates["mycdek_pvz_234"]); //экономичный склад-склад им
    } elseif(isset($rates["mycdek_pvz_234"]) && isset($rates["mycdek_pvz_136"])) {
      unset($rates["mycdek_pvz_136"]); //склад-склад им
    }

    if (isset($rates["mycdek_pvz_postamat_378"]) && empty($rates["mycdek_pvz_postamat_378"]->get_cost())) {
      unset($rates["mycdek_pvz_postamat_378"]); //экономичный склад-постамат им
    } elseif(isset($rates["mycdek_pvz_postamat_378"]) && isset($rates["mycdek_pvz_postamat_368"])) {
      unset($rates["mycdek_pvz_postamat_368"]); //склад-постамат им
    }

    //убираем тяжеловес если он пуст, показываем если не пуст
    if (isset($rates["mycdek_pvz_15"]) && empty($rates["mycdek_pvz_15"]->get_cost())) {
      unset($rates["mycdek_pvz_15"]); //тяжеловес склад-склад
    }

    if (isset($rates["mycdek_16"]) && empty($rates["mycdek_16"]->get_cost())) {
      unset($rates["mycdek_16"]); //тяжеловес склад-дверь
    }

    return $rates;
  },
  10,
  2
);