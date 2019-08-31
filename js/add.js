$(() => {
  let positionNumber = 0;
  $("#add-position").click(event => {
    event.preventDefault();

    if ($("#positions").children().length < 9) {
      $("#positions").append(`<div id="position${++positionNumber}">
        <p>Year: <input type="text" name="year${positionNumber}" value="">
        <input type="button" value="-" onclick="$('#position${positionNumber}').remove();return false;"></p>
        <textarea name="desc${positionNumber}" rows="8" cols="80"></textarea>
        </div>
      </div>`);
    } else {
      alert("Maximum of nine position entries exceeded");
    }
  });
});
