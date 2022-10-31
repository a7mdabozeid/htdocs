declare var jQuery: any;
declare var acfw_edit_coupon: any;
declare var wc_memberships_args: any;

(($) => {
    const {
        allowed_field_title,
        disallowed_field_title,
        select_membership_plans,
        membership_plans,
    } = wc_memberships_args;

    /**
     * Generate WC Memberships condition field data.
     *
     * @param key
     * @returns
     */
    function generate_field_data(key: string, title: string) {
        return {
            title: title,
            group: "customers",
            key: key,
            default_data_value: [],

            /**
             * Condition field template markup callback.
             *
             * @since 1.15
             */
            template_callback: (
                data: any,
                field_key: string = "wc_memberships_allowed"
            ) => {
                return `
                <div class="wc-memberships-field condition-field" data-type="${key}">
                    <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
                    <h3 class="condition-field-title">${title}</h3>
                    <div class="field-control">
                        <select class="wc-enhanced-select condition-value" multiple data-placeholder="${select_membership_plans}">
                            ${get_membership_options(data)}
                        </select>
                    </div>
                </div>        
                `;
            },

            /**
             * Condition field scraper callback.
             *
             * @since 1.15
             */
            scraper_callback: (condition_field: HTMLElement) => {
                const temp: HTMLSelectElement =
                    condition_field.querySelector(".condition-value");
                return $(temp).val();
            },
        };
    }

    /**
     * Register condition field callbacks.
     */
    acfw_edit_coupon.cart_condition_fields.wc_memberships_allowed =
        generate_field_data("wc-memberships-allowed", allowed_field_title);
    acfw_edit_coupon.cart_condition_fields.wc_memberships_disallowed =
        generate_field_data(
            "wc-memberships-disallowed",
            disallowed_field_title
        );
    acfw_edit_coupon.cart_conditon_field_options.push("wc-memberships-allowed");
    acfw_edit_coupon.cart_conditon_field_options.push(
        "wc-memberships-disallowed"
    );

    /*
    |--------------------------------------------------------------------------
    | Utility functions.
    |--------------------------------------------------------------------------
    */

    /**
     * Get membership options markup.
     *
     * @since 1.15
     *
     * @param data
     */
    function get_membership_options(data: any = []): string {
        let markup = "";

        for (let key in membership_plans) {
            const label = membership_plans[key];
            const selected = data.indexOf(key) > -1 ? "selected" : "";
            markup += `<option value="${key}" ${selected}>${label}</option>`;
        }

        return markup;
    }
})(jQuery);
