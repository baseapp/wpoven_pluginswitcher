(function ($) {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var element = document.querySelector("tr.add-rule-button");
    if (element) {
      var firstAElement = element.querySelector("th");
      if (firstAElement) {
        firstAElement.remove();
      }
    }

    var element = document.querySelector(
      "li.toplevel_page_wpoven-plugin-switcher"
    );
    if (element) {
      element.remove();
    }

    function checkVisibilityAndRefresh() {
      let savedNotice = document.querySelector(
        ".saved_notice.admin-notice.notice-green"
      );

      let errorNotices = document.querySelector(
        ".redux-field-errors.notice-red"
      );

      if (
        savedNotice &&
        window.getComputedStyle(savedNotice).display !== "none"
      ) {
        if (window.getComputedStyle(errorNotices).display !== "block") {
          window.location.reload();
        }
      }
    }
    // Check every 500 milliseconds (0.5 seconds)
    setInterval(checkVisibilityAndRefresh, 1000);

    //remove extra menu title
    const menuItems = document.querySelectorAll("li#toplevel_page_wpoven");
    const menuArray = Array.from(menuItems);
    for (let i = 1; i < menuArray.length; i++) {
      menuArray[i].remove();
    }

    function getQueryParam(param) {
      let urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(param);
    }

    let pageValue = getQueryParam("page");
    let ruleValue = getQueryParam("rule");

    if (!localStorage.getItem("formSubmitted")) {
      var form = document.createElement("form");
      form.setAttribute("method", "post");

      var createField = document.createElement("input");
      createField.setAttribute("type", "text");
      createField.setAttribute("name", "rule");
      createField.setAttribute("value", ruleValue);
      form.appendChild(createField);

      document.body.appendChild(form);

      // Set a flag in local storage to indicate the form has been submitted
      localStorage.setItem("formSubmitted", "true");

      form.submit();

      return false;
    }

    var elements = document.querySelectorAll(
      "div.redux-accordion-info.form-table-accordion"
    );

    elements.forEach(function (element) {
      var h3 = element.querySelector("h3");
      // Check if the icon already exists
      if (h3 && !h3.querySelector(".icon-container.copy")) {
        var icon = document.createElement("img");
        icon.className = "icon-container copy";
        icon.src = myPluginData.iconUrl;
        h3.appendChild(icon);
      }
    });

    // Function to toggle accordion
    function toggleAccordion(element) {
      var accordionWrap = element.nextElementSibling;
      if (
        accordionWrap.style.display === "none" ||
        !accordionWrap.style.display
      ) {
        accordionWrap.style.display = "block";
      } else {
        accordionWrap.style.display = "none";
      }

      var otherAccordions = document.querySelectorAll(".redux-accordion-wrap");
      otherAccordions.forEach(function (accordion) {
        if (accordion !== accordionWrap) {
          accordion.style.display = "none";
        }
      });
    }

    // Add click event listener to accordion headers
    var headers = document.querySelectorAll(".redux-accordion-info h3");
    headers.forEach(function (header) {
      header.addEventListener("click", function (event) {
        if (!event.target.classList.contains("icon-container")) {
          toggleAccordion(this.parentElement);
        }
      });
    });

    // Add click event listener to copy icons
    var copyIcons = document.querySelectorAll(".icon-container.copy");
    copyIcons.forEach(function (icon) {
      icon.addEventListener("click", function (event) {
        event.stopPropagation(); // Prevent triggering the accordion toggle

        // Copy link to clipboard
        var element = this.closest(
          ".redux-accordion-info.form-table-accordion"
        );
        var text = element.querySelector(".redux-accordion-desc").textContent;
        var textarea = document.createElement("textarea");
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);

        // Remove existing "Copied" text if it exists
        var existingCopiedText =
          this.parentElement.querySelector(".copied-text");
        if (existingCopiedText) {
          existingCopiedText.remove();
        }

        // Add "Copied" text
        var copiedText = document.createElement("span");
        copiedText.className = "copied-text";
        copiedText.textContent = "Link copied";
        this.parentElement.appendChild(copiedText);

        // Remove "Copied" text after 2 seconds
        setTimeout(function () {
          copiedText.remove();
        }, 1000);
      });
    });
  });

  $(function () {
    $("#add-rule-button-buttonsetenabled")
      .parent()
      .click(function (event) {
        // Prompt the user to enter some text
        var userInput = prompt("Please enter rule name:");

        if (userInput === null) {
          return; //break out of the function early
        }

        // Check if the user entered something
        if (userInput) {
          // Display the entered text in the 'displayText' div

          event.preventDefault();
          event.stopImmediatePropagation();

          var form = document.createElement("form");
          form.setAttribute("method", "post");

          var createField = document.createElement("input");
          createField.setAttribute("type", "text");
          createField.setAttribute("name", "rule_name");
          createField.setAttribute("value", userInput);

          form.appendChild(createField);
          document.body.appendChild(form);

          form.submit();

          return false;
        } else {
          // Handle the case where the user pressed cancel or entered nothing
          alert("No text was entered.");
        }
      });
  });
})(jQuery);
