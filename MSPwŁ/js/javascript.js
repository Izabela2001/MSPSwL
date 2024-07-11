const hamburger = document.querySelector(".hamburger_menu");
const closeIcon = document.querySelector(".closeIcon");
const menu = document.querySelector(".nav_link");

function toggleMenu() {
  menu.classList.toggle("active");
  closeIcon.classList.toggle("show"); 
}

hamburger.addEventListener("click", toggleMenu);
closeIcon.addEventListener("click", toggleMenu);

function toggleEditForm(id) {
  var form = document.getElementById('edit-form-' + id);
  if (form.style.display === 'none') {
      form.style.display = 'block';
  } else {
      form.style.display = 'none';
  }
}

function toggleEditFormRating(id) {
  var form = document.getElementById('edit-form-rating-' + id);
  if (form.style.display === 'none') {
      form.style.display = 'block';
  } else {
      form.style.display = 'none';
  }
}

function validateForm(id) {
  var textarea = document.getElementById('nowa-opinia-' + id);
  if (textarea.value.trim() === '') {
      alert('Treść opinii nie może być pusta.');
      return false;
  }
  if (textarea.value.length > 500) {
      alert('Treść opinii nie może przekraczać 500 znaków.');
      return false;
  }
  return true;
}

function validateRatingForm(id) {
  var select = document.querySelector('#edit-form-rating-' + id + ' select[name="Ocena"]');
  if (!select.value || select.value < 1 || select.value > 5) {
      alert('Ocena musi być liczbą od 1 do 5.');
      return false;
  }
  return true;
}



