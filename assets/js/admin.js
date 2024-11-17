document.addEventListener("DOMContentLoaded", function () {
  const wrapper = document.querySelector(".icon-select-wrapper");
  const current = wrapper.querySelector(".icon-select-current");
  const options = wrapper.querySelector(".icon-select-options");
  const input = wrapper.querySelector('input[type="hidden"]');

  current.addEventListener("click", function (e) {
    options.style.display =
      options.style.display === "block" ? "none" : "block";
    e.stopPropagation();
  });

  options.querySelectorAll(".icon-option").forEach((option) => {
    option.addEventListener("click", function () {
      const value = this.dataset.value;
      const icon = this.querySelector("i");
      const text = this.querySelector("span").textContent;

      input.value = value;

      current.innerHTML = value
        ? `<i class="${icon.className}"></i><span>${text}</span>`
        : `<span>${text}</span>`;

      options.style.display = "none";
    });
  });

  document.addEventListener("click", function (e) {
    if (!wrapper.contains(e.target)) {
      options.style.display = "none";
    }
  });
});
