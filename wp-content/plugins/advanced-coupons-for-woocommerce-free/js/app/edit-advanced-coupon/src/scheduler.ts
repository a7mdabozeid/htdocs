declare var jQuery: any;
declare var flatpickr: any;
declare var acfw_edit_coupon: any;
declare var vex: any;

const $ = jQuery;
let isValidating = false;

/**
 * Edit link scheduler fields script.
 *
 * @since 2.0
 *
 * @param object $ jQuery object.
 */
export default function edit_link_scheduler_fields() {
  const scheduler_tab: HTMLElement = document.querySelector("#acfw_scheduler"),
    schedule_start_field: HTMLInputElement = scheduler_tab.querySelector(
      "#_acfw_schedule_start"
    ),
    schedule_expire_field: HTMLInputElement = scheduler_tab.querySelector(
      "#_acfw_schedule_expire"
    ),
    wc_default_expiry_field: HTMLInputElement = document.querySelector(
      "#general_coupon_data p.expiry_date_field"
    );

  $(scheduler_tab).on(
    "change",
    ".date-field,.date-hour,.date-minute",
    toggle_fields_required_prop
  );

  $(scheduler_tab).on(
    "click",
    ".clear-scheduler-fields",
    clear_scheduler_fields_values
  );

  // hide the default WC coupon expiry date field from the DOM.
  $(wc_default_expiry_field).css("display", "none");

  $(schedule_start_field).trigger("change acfw_load");
  $(schedule_expire_field).trigger("change acfw_load");

  $(scheduler_tab).on(
    "change acfw_load",
    ".acfw-section-toggle input",
    toggleSchedulerSection
  );
  $(scheduler_tab).find(".acfw-section-toggle input").trigger("acfw_load");

  $(".wrap").on("submit", "form#post", validateBeforeSaveCoupon);

  initDateFields();
}

/**
 * Initialize date and time picker fields.
 *
 * @since 4.5
 */
function initDateFields() {
  const scheduleStartField: any = document.querySelector(
    "#_acfw_schedule_start"
  );
  const scheduleExpireField: any = document.querySelector(
    "#_acfw_schedule_expire"
  );

  // create date objects for fields default min/max values.
  const startMaxDate =
    flatpickr.parseDate(scheduleExpireField.value, "Y-m-d H:i:S") ?? null;
  const expireMinDate =
    flatpickr.parseDate(scheduleStartField.value, "Y-m-d H:i:S") ?? null;

  // add/deduct 1 minute to the min/max values.
  if (startMaxDate) startMaxDate.setMinutes(startMaxDate.getMinutes() - 1);
  if (expireMinDate) expireMinDate.setMinutes(expireMinDate.getMinutes() + 1);

  $(scheduleStartField).flatpickr({
    allowInput: true,
    enableTime: true,
    altInput: true,
    allowInvalidPreload: true,
    altFormat: "Y-m-d h:i K",
    dateFormat: "Y-m-d H:i:S",
    maxDate: startMaxDate,

    // change minimum datetime for expire field when start field value changes.
    onChange: function ([dateObject]: Date[], dateStr: string, instance: any) {
      if (dateObject) {
        dateObject.setMinutes(dateObject.getMinutes() + 1);
        scheduleExpireField._flatpickr.set("minDate", dateObject);

        validate_date_range_field_values();
      }
    },
  });

  $(scheduleExpireField).flatpickr({
    allowInput: true,
    enableTime: true,
    altInput: true,
    altFormat: "Y-m-d h:i K",
    dateFormat: "Y-m-d H:i:S",
    minDate: expireMinDate,

    // change maximum datetime for start field when expire field value changes.
    onChange: function ([dateObject]: Date[], dateStr: string, instance: any) {
      if (dateObject) {
        dateObject.setMinutes(dateObject.getMinutes() - 1);
        scheduleStartField._flatpickr.set("maxDate", dateObject);

        validate_date_range_field_values();
      }
    },
  });
}

/**
 * Validate date range field values when the values are changed.
 *
 * @since 4.5
 */
function validate_date_range_field_values() {
  const startDate = flatpickr.parseDate(
    $("#_acfw_schedule_start").val(),
    "Y-m-d H:i:S"
  );
  const expireDate = flatpickr.parseDate(
    $("#_acfw_schedule_expire").val(),
    "Y-m-d H:i:S"
  );

  if (startDate >= expireDate) {
    $("#_acfw_schedule_start").parent().addClass("error");
    $("#_acfw_schedule_expire").parent().addClass("error");
  } else {
    $("#_acfw_schedule_start").parent().removeClass("error");
    $("#_acfw_schedule_expire").parent().removeClass("error");
  }
}

/**
 * Prevent saving coupon when there is an error in the date fields.
 *
 * @param e Event object
 */
function validateBeforeSaveCoupon(e: any) {
  if (
    $(".acfw-date-range-schedules-section span.date-time-field").hasClass(
      "error"
    )
  ) {
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
 * Toggle scheduler fields required prop.
 * Set to true when at least one of the date/time fields have value, false when all fields are blank.
 *
 * @since 2.1
 */
function toggle_fields_required_prop() {
  const $parent = $(this).closest(".date-time-field"),
    $date = $parent.find(".date-field");

  if ($date.val()) {
    $date.prop("required", true);
  } else {
    $date.prop("required", false);
  }
}

/**
 * Clear scheduler field values and set to not required.
 *
 * @since 2.1
 */
function clear_scheduler_fields_values() {
  const scheduler_tab: HTMLElement = document.querySelector("#acfw_scheduler"),
    schedule_start_field: HTMLInputElement = scheduler_tab.querySelector(
      "#_acfw_schedule_start"
    ),
    schedule_expire_field: HTMLInputElement = scheduler_tab.querySelector(
      "#_acfw_schedule_expire"
    );

  const $datefield = $(this).siblings(".date-time-field");
  $datefield.find("input").prop("required", false).val("");

  $(schedule_start_field).trigger("change");
  $(schedule_expire_field).trigger("change");
}

/**
 * Toggle scheduler section fields when main section checkbox is checked/unchecked.
 *
 * @since 4.5
 */
function toggleSchedulerSection() {
  const $input = $(this);
  const $section = $input.closest(".acfw-scheduler-section");
  const $options = $section.find(".options_group");

  if ($input.is(":checked")) {
    $options.removeClass("disabled");
    $options
      .find(".date-field,.days-time-field label input,textarea")
      .prop("disabled", false);
    $("#acfw_scheduler .options_group input").trigger("acfw_load");
  } else {
    $options.addClass("disabled");
    $options
      .find(".date-field,.days-time-field label input,textarea")
      .prop("disabled", true);
  }

  $options.find("input").trigger("change");
}
