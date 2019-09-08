$(() => {
  let positionNumber = 0;
  $("#add-position").click(event => {
    event.preventDefault();

    if ($("#positions").children().length < 9) {
      $("#positions").append(`<div id="position${++positionNumber}">
          <p>Year: <input type="text" name="year${positionNumber}" value="">
          <input type="button" id="minusButton${positionNumber}" value="-"></p>
          <textarea name="desc${positionNumber}" rows="8" cols="80"></textarea>
          </div>
        </div>`);

      $("#minusButton" + positionNumber).click(() => {
        $("#position" + positionNumber).remove();
      });
    } else {
      alert("Maximum of nine position entries exceeded");
    }
  });

  let educationNumber = 0;
  $("#add-education").click(event => {
    event.preventDefault();

    if ($("#education").children().length < 9) {
      $("#education").append(
        '<div id="educationId' +
          educationNumber +
          '"><p>Year: <input type="text" name="eduYear' +
          educationNumber +
          '" value=""><input type="button" value="-" onclick="$(\'#educationId' +
          educationNumber.toString() +
          '\').remove();return false;"></p><p>School: <input type="text" size="80" name="school' +
          educationNumber +
          '" class="school" value="" /></p></div></div>'
      );

      $(".school").autocomplete({
        source: "school.php"
      });
    } else {
      alert("Maximum of nine position entries exceeded");
    }
  });
});
