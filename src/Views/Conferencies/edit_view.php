<?php if (!isset($data['title'])) { ?>
    <p>К сожалению, нет конференции с таким id</p>
    <a href="/" class="btn btn-secondary">На главную</a>
<?php } else { ?>
    <button class="btn btn-secondary mb-2 pt-0 pb-0" onclick="history.back();">Назад</button>
    <h2>Редактирование конференции</h2>
    <form method="post">
        <input type="hidden" name="lat" id="lat" <?php if ($data["addressIsSet"]) { ?>value="<?= $data["lat"] ?>" <?php } ?>>
        <input type="hidden" name="lng" id="lng" <?php if ($data["addressIsSet"]) { ?>value="<?= $data["lng"] ?>" <?php } ?>>

        <div class="form-group">
            <label for="title">Название:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
            <?php if ($data['errors']['title']) { ?>
                <small id="title_help" class="form-text text-danger"><?= $data['errors']['title'] ?></small>
            <?php } ?>
        </div>

        <div class="form-group">
            <label for="conductDate" class="col-md-3">Дата конференции:</label>
            <input id="conductDate" name="conductDate" value="<?= $data['conductDate'] ?>" class="col-md-3" required>
            <?php if ($data['errors']['conductDate']) { ?>
                <small id="conduct_date_help" class="form-text text-danger pl-3"><?= $data['errors']['conductDate'] ?></small>
            <?php } ?>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script>
                flatpickr("#conductDate", {
                    minDate: "today"
                });
            </script>
        </div>

        <div class="form-group">
            <label for="country" class="col-md-3">Страна:</label>
            <select id="countryId" name="countryId" class="col-md-3" required>
                <option value="0">-------------------</option>
                <?php foreach ($data['countries'] as $country) : ?>
                    <option value="<?= $country['id'] ?>" <?php if ($country['id'] == $data['countryId']) { ?>selected<?php } ?>>
                        <?= $country['title'] ?>
                    </option>
                <?php endforeach ?>
            </select>
            <?php if ($data['errors']['countryId']) { ?>
                <small id="country_help" class="form-text text-danger pl-3"><?= $data['errors']['countryId'] ?></small>
            <?php } ?>
        </div>

        <label for="map" class="pl-2">
            <h4><small>Адрес: <?= $data["lat"] and $data["lng"] ? "" : "не указан" ?></small></h4>
        </label>
        <div class="wrap_map">
            <div id="map" class="w-100 h-100"></div>

            <script>
                function initMap() {
                    var marker;
                    var btn_remove_marker = document.getElementById("btn-remove-marker");
                    var remove_marker_listener;

                    var map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 10,
                        center: {
                            lat: <?= $data['addressIsSet'] ? $data["lat"] : 50.450001 ?>,
                            lng: <?= $data['addressIsSet'] ? $data["lng"] : 30.517023 ?>
                        }
                    });

                    <?php if ($data['addressIsSet']) { ?>
                        marker = new google.maps.Marker({
                            map: map,
                            position: {
                                lat: <?= $data["lat"] ?>,
                                lng: <?= $data["lng"] ?>
                            },
                            draggable: true
                        });

                        remove_marker_listener = removeMarker;
                        btn_remove_marker.addEventListener("click", remove_marker_listener);
                    <?php } ?>

                    var latElem = document.getElementById("lat");
                    var lngElem = document.getElementById("lng");

                    map.addListener('click', function(e) {
                        moveMarker(e.latLng);
                    });

                    function moveMarker(latLng) {
                        var lat = latLng.lat();
                        var lng = latLng.lng();

                        if (marker) {
                            var markerPosition = marker.getPosition();
                            var is_same_marker = (lat === markerPosition.lat() && lng === markerPosition.lng());

                            removeMarker();

                            if (is_same_marker) {
                                return;
                            }
                        }

                        marker = new google.maps.Marker({
                            map: map,
                            position: latLng,
                            draggable: true
                        });

                        if (remove_marker_listener) {
                            btn_remove_marker.removeEventListener("click", remove_marker_listener, false);
                        }

                        remove_marker_listener = removeMarker;
                        btn_remove_marker.addEventListener("click", remove_marker_listener);

                        latElem.value = lat;
                        lngElem.value = lng;
                    }

                    function removeMarker() {
                        marker.setMap(null);
                        marker = null;

                        latElem.value = "";
                        lngElem.value = "";

                        btn_remove_marker.removeEventListener("click", remove_marker_listener, false);
                        remove_marker_listener = null;
                    }
                }
            </script>
        </div>
        <small id="map_lat_help" class="form-text text-danger"><?= $data['errors']['lat'] ?></small>
        <small id="map_lng_help" class="form-text text-danger"><?= $data['errors']['lng'] ?></small>

        <div class="row justify-content-between mt-3 pl-3 pr-3">
            <a href="<?= URL_ROOT ?>/conferencies/delete/<?= $data['id'] ?>" class="btn btn-danger">Удалить</a>
            <div>
                <div class="btn btn-secondary" id="btn-remove-marker">Убрать маркер</div>
                <button type="submit" class="btn btn-primary ml-2">Сохранить</button>
            </div>
        </div>

    </form>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initMap&v=weekly" defer></script>
<?php } ?>