declare var jQuery: any;
declare var acfw_edit_coupon: any;
declare var vex: any;

const $ = jQuery;
let isValidating = false;

/**
 * Edit link scheduler fields script.
 *
 * @since 2.0
 * @since 3.5 Moved date range scheduler related code to ACFWF.
 *
 * @param object $ jQuery object.
 */
export default function edit_link_scheduler_fields() {
  const scheduler_tab: HTMLElement = document.querySelector("#acfw_scheduler");

  $(scheduler_tab).on(
    "change acfw_load",
    '.days-time-fields input[type="checkbox"]',
    toggleDayTimeFields
  );

  $(scheduler_tab).on(
    "change acfw_load",
    ".days-time-fields input.time-field",
    updateTimeMinMaxValues
  );

  $(scheduler_tab).on(
    "change",
    ".days-time-fields input.time-field",
    validateTimeInputFields
  );

  $(scheduler_tab).on(
    "change acfw_load",
    ".acfw-scheduler-section .date-field",
    toggleAvailableDayFields
  );

  $(scheduler_tab)
    .find(
      '.days-time-fields input[type="checkbox"], .days-time-fields input.time-field'
    )
    .trigger("acfw_load");

  $(scheduler_tab)
    .find(".acfw-scheduler-section .date-field")
    .trigger("acfw_load");

  $(".wrap").on("submit", "form#post", validateBeforeSaveCoupon);

  initTimeFields();
}

function initTimeFields() {
  $(".days-time-fields input.time-field").flatpickr({
    allowInput: true,
    enableTime: true,
    noCalendar: true,
    altInput: true,
    altFormat: "h:i K",
    dateFormat: "H:i",
    onOpen: function (selectedDates: Date[], dateStr: string, instance: any) {
      if ("" === dateStr) {
        var defaultTime = new Date();
        defaultTime.setHours(0);
        defaultTime.setMinutes(0);
        instance.setDate(defaultTime);

        $(".days-time-fields input.time-field").trigger("change");
      }
    },
  });
}

function validateBeforeSaveCoupon(e: any) {
  if ($(".days-time-fields input.time-field").hasClass("error")) {
    e.preventDefault();

    // display error message.
    vex.dialog.alert({
      unsafeMessage: acfw_edit_coupon.invalid_scheduler_time,
    });

    // re-enable publish button
    $("#publishing-action .spinner").removeClass("is-active");
    $("#publishing-action #publish").removeClass("disabled");
  }
}

/**
 * Handle enable/disable of time input fields when the relative day checkbox is checked/unchecked.
 *
 * @since 3.5
 */
function toggleDayTimeFields() {
  const $checkbox = $(this);
  const $wrapper = $checkbox.closest(".days-time-field");

  $wrapper
    .find(".start-time")
    .prop("disabled", !$checkbox.is(":checked") || $checkbox.is(":disabled"));
  $wrapper
    .find(".end-time")
    .prop("disabled", !$checkbox.is(":checked") || $checkbox.is(":disabled"));
}

/**
 * Update the min/max attributes of the time input fields whenever its relative value is changed.
 *
 * @since 3.5
 */
function updateTimeMinMaxValues() {
  const $wrapper = $(this).closest(".days-time-field");
  const $startTime = $wrapper.find(".start-time");
  const $endTime = $wrapper.find(".end-time");
  const endMinutes = getMinutesTotal($endTime.val()) + 1;
  const startMinutes = getMinutesTotal($startTime.val()) - 1;
  const maxTime = endMinutes ? getTimeFromMinutes(endMinutes) : "";
  const minTime = startMinutes ? getTimeFromMinutes(startMinutes) : "";

  $startTime.attr("max", maxTime ?? "");
  $endTime.attr("min", minTime ?? "");
}

/**
 * Validate time input fields by adding a red border when the time values set are invalid.
 *
 * @since 3.5
 */
function validateTimeInputFields() {
  const $wrapper = $(this).closest(".days-time-field");
  const $startTime = $wrapper.find(".start-time");
  const $endTime = $wrapper.find(".end-time");
  const startMinutes = getMinutesTotal($startTime.val());
  const endMinutes = getMinutesTotal($endTime.val());

  if (startMinutes >= endMinutes) {
    $startTime.addClass("error");
  } else {
    $startTime.removeClass("error");
  }

  if (endMinutes <= startMinutes) {
    $endTime.addClass("error");
  } else {
    $endTime.removeClass("error");
  }
}

/**
 * Toggle enable/disable for the time fields based on the start day and end day date range.
 *
 * @since 3.5
 */
function toggleAvailableDayFields() {
  const $wrapper = $(this).closest(".options_group");
  const $startDay = $wrapper.find("#_acfw_schedule_start");
  const $endDay = $wrapper.find("#_acfw_schedule_expire");

  const availableDays = getAvailableDaysFromRange(
    !$startDay.is(":disabled") ? $startDay.val() : "",
    !$startDay.is(":disabled") ? $endDay.val() : ""
  );

  getDays().forEach((day) => {
    const $fieldWrapper = $(`.${day}-time-field`);

    if (availableDays.includes(day)) {
      $fieldWrapper
        .find('input[type="checkbox"]')
        .prop("disabled", false)
        .trigger("change");
    } else {
      $fieldWrapper.find('input[type="checkbox"]').prop("disabled", true);
      $fieldWrapper.find(".time-field").prop("disabled", true);
    }
  });
}

/**
 * Get the total minutes for a given time.
 *
 * @since 3.5
 *
 * @param {string} time Time value e.g '15:00'
 * @returns {int} Total minutes
 */
function getMinutesTotal(time: string) {
  const [hour, minute] = time.split(":").map((n) => parseInt(n));
  return hour * 60 + minute;
}

/**
 * Get the time value by a given total minutes value.
 *
 * @since 3.5
 *
 * @param {int} minutes Total minutes value
 * @returns {string} Time value
 */
function getTimeFromMinutes(minutes: number) {
  const hour = Math.floor(minutes / 60);
  const minute = minutes - hour * 60;

  return `${String(hour).padStart(2, "0")}:${String(minute).padStart(2, "0")}`;
}

/**
 * Get available days based on a given start day and end day date range.
 *
 * @since 3.5
 *
 * @param {string} startDay Start day (e.g. 2022-08-01)
 * @param {string} endDay End day (e.g. 2022-08-31)
 * @returns {string[]} List of available days
 */
function getAvailableDaysFromRange(startDay: string, endDay: string) {
  const days = getDays();
  const availableDays = [];

  // return all days when either startDay or enDay values are not available.
  if (!startDay || !endDay) {
    return days;
  }

  for (
    const d = new Date(startDay);
    d <= new Date(endDay);
    d.setDate(d.getDate() + 1)
  ) {
    availableDays.push(days[d.getDay()]);
  }

  return [...new Set(availableDays)];
}

/**
 * Return a list of all days in a week.
 *
 * @returns {string[]} List of days in a week.
 */
function getDays() {
  return [
    "sunday",
    "monday",
    "tuesday",
    "wednesday",
    "thursday",
    "friday",
    "saturday",
  ];
}
