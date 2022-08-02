<?php if ((!isset($data['title']))) { ?>
    <p>К сожалению, нет конференции с таким id</p>
    <a href="/" class="btn btn-secondary">На главную</a>
<?php } else { ?>
    <h3 class="mb-3"><?= htmlspecialchars($data["title"]) ?></a></h3>
    <div class="conference_info mb-4">
        <p class="mb-0">Дата проведения: <?= $data["conductDate"] ?></p>
        <p class="mb-0">Страна: <?= $data["countryTitle"] ?></p>
    </div>

    <?php if (!$data['addressIsSet']) { ?>
        <div class="pl-2">
            <h4><small>Адрес: не указан</small></h4>
        </div>
    <?php } else { ?>
        <label for="map" class="pl-2">
            <h4><small>Адрес:</small></h4>
        </label>
        <div class="wrap_map">
            <div id="map" class="w-100 h-100"></div>
            <script>
                function initMap() {
                    const latLng = {
                        lat: <?= $data["lat"] ?>,
                        lng: <?= $data["lng"] ?>
                    };
                    const map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 11,
                        center: latLng,
                    });

                    const marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                    });
                }

                window.initMap = initMap;
            </script>
        </div>
    <?php } ?>

    <div class="row justify-content-between mt-3 pl-3 pr-3">
        <a href="<?= URL_ROOT ?>/conferencies/delete/<?= $data['id'] ?>">
            <div class="btn btn-danger">Удалить</div>
        </a>
        <a href="<?= URL_ROOT ?>/conferencies/edit/<?= $data['id'] ?>">
            <div class="btn btn-primary ml-2">Редактировать</div>
        </a>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initMap&v=weekly" defer></script>
<?php } ?>