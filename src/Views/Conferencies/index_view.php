<h2>Список всех конференций</h2>
<div class="conferencies mt-4">
  <?php foreach ($data["conferencies"] as $conference) : ?>

    <div class="card conference mb-2" id="conference__<?= $conference["id"] ?>">
      <h5 class="card-header pl-3 pr-3 pt-2 pb-2"><?= htmlspecialchars($conference["title"]) ?></h5>
      <div class="card-body p-3">
        <h6 class="card-subtitle mb-2 text-muted"><?= $conference["conductDate"] ?></h6>
        <a href="<?= URL_ROOT ?>/conferencies/detail/<?= $conference['id'] ?>" class="btn btn-primary mr-2">Подробнее</a>
        <a href="<?= URL_ROOT ?>/conferencies/delete/<?= $conference['id'] ?>" class="btn btn-danger btn-delete-conference">Удалить</a>
      </div>
    </div>

  <?php endforeach; ?>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function(e) {
    var btn_delete_list = document.getElementsByClassName("btn-delete-conference");

    function deleteConferenceEventHandler(event) {
      event.preventDefault();

      var conference = event.target.closest(".conference");
      var conference_id = conference.id.replace("conference__", "");

      url = `${location.protocol}//${location.host}/conferencies/delete/${conference_id}`;

      fetch(url, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: {
            'id': conference_id,
          }
        })
        .then(response => response.json())
        .then(json => deleteConferenceRender(json, conference));
    }

    function deleteConferenceRender(data, conference) {
      if (data["state"] == "success") {
        conference.remove();
        toastr.success("Конференция удалена");
      } else {
        toastr.error("Произошла ошибка при удалении конференции");
      }
    }

    for (var i = 0; i < btn_delete_list.length; i++) {
      btn_delete_list[i].addEventListener('click', deleteConferenceEventHandler);
    }
  });
</script>