async function submitForm() {
  let loginbox = document.getElementById("loginbox");
  let loading = document.getElementById("loading");
  let footer = document.getElementById("footer");
  loginbox.style.display = 'none'
  loading.style.display = 'block'
  footer.style.display = 'none'
  let user = encodeURIComponent(document.getElementById("user").value);
  let password = encodeURIComponent(document.getElementById("password").value);
  try {
    const response = await fetch(`/auth/email/login?user=${user}&password=${password}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json"
      },
    });

    const data = await response.json();

    if (data.success === true) {
      window.location.href = '/dashboard';
    } else {
      toastr.error(data.message, 'Error');
      loginbox.style.display = 'block'
      loading.style.display = 'none'
      footer.style.display = 'block'
    }
  } catch (error) {
    toastr.error(error, "Error!");
    loginbox.style.display = 'block'
    loading.style.display = 'none'
    footer.style.display = 'block'
  }
};
