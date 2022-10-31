<?php

namespace WPML\Core;

use \WPML\Core\Twig\Environment;
use \WPML\Core\Twig\Error\LoaderError;
use \WPML\Core\Twig\Error\RuntimeError;
use \WPML\Core\Twig\Markup;
use \WPML\Core\Twig\Sandbox\SecurityError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedTagError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFilterError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFunctionError;
use \WPML\Core\Twig\Source;
use \WPML\Core\Twig\Template;

/* settings-ui.twig */
class __TwigTemplate_8080734e74432fc946ed4d6c2a57e62c7a234db45911e26d96e5ce09fd2f84ce extends \WPML\Core\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<form method=\"post\" action=\"";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "action", []), "html", null, true);
        echo "\">

    ";
        // line 3
        if ($this->getAttribute(($context["form"] ?? null), "translation_interface", [])) {
            // line 4
            echo "        <div class=\"wcml-section\">
            <div class=\"wcml-section-header\">
                <h3>
                    ";
            // line 7
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "heading", []), "html", null, true);
            echo "
                    <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
            // line 8
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "tip", []), "html", null, true);
            echo "\"></i>
                </h3>
            </div>
            <div class=\"wcml-section-content\">

                <div id=\"wcml-translation-interface-dialog-confirm\" title=\"";
            // line 13
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "heading", []), "html", null, true);
            echo "\" class=\"hidden\">
                    <p>";
            // line 14
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "pb_warning", []), "html", null, true);
            echo "</p>
                    <input type=\"hidden\" class=\"ok-button\" value=\"";
            // line 15
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "pb_warning_ok_button", []), "html", null, true);
            echo "\" />
                    <input type=\"hidden\" class=\"cancel-button\" value=\"";
            // line 16
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "pb_warning_cancel_button", []), "html", null, true);
            echo "\"/>
                </div>

                <ul>
                    <li>
                        <input type=\"radio\" name=\"trnsl_interface\" value=\"";
            // line 21
            echo \WPML\Core\twig_escape_filter($this->env, ($context["wpml_translation"] ?? null), "html", null, true);
            echo "\"
                                ";
            // line 22
            if (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "controls_value", []) == ($context["wpml_translation"] ?? null))) {
                echo " checked=\"checked\"";
            }
            echo " id=\"wcml_trsl_interface_wcml\" />
                        <label for=\"wcml_trsl_interface_wcml\">";
            // line 23
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "wcml", []), "label", []), "html", null, true);
            echo "</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"trnsl_interface\" value=\"";
            // line 26
            echo \WPML\Core\twig_escape_filter($this->env, ($context["native_translation"] ?? null), "html", null, true);
            echo "\"
                                ";
            // line 27
            if (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "controls_value", []) == ($context["native_translation"] ?? null))) {
                echo " checked=\"checked\"";
            }
            echo " id=\"wcml_trsl_interface_native\" />
                        <label for=\"wcml_trsl_interface_native\">";
            // line 28
            echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "translation_interface", []), "native", []), "label", []);
            echo "</label>
                    </li>
                </ul>

            </div> <!-- .wcml-section-content -->

        </div> <!-- .wcml-section -->
    ";
        }
        // line 36
        echo "    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 40
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "heading", []), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 41
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "tip", []), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_date\" value=\"1\"
                            ";
        // line 50
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "sync_date", []), "value", []) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_products_sync_date\" />
                    <label for=\"wcml_products_sync_date\">";
        // line 51
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "sync_date", []), "label", []), "html", null, true);
        echo "</label>
                </li>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_order\" value=\"1\"
                            ";
        // line 55
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "sync_order", []), "value", []) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_products_sync_order\" />
                    <label for=\"wcml_products_sync_order\">";
        // line 56
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "synchronization", []), "sync_order", []), "label", []), "html", null, true);
        echo "</label>
                </li>
            </ul>

        </div>

    </div>

    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 68
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "media_synchronization", []), "heading", []), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 69
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "media_synchronization", []), "tip", []), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"sync_media\" value=\"1\"
                            ";
        // line 78
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "media_synchronization", []), "sync_media", []), "value", []) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_sync_media\" />
                    <label for=\"wcml_sync_media\">";
        // line 79
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "media_synchronization", []), "sync_media", []), "label", []), "html", null, true);
        echo "</label>
                </li>
            </ul>

        </div>

    </div>


    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 92
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "heading", []), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 93
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "tip", []), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"1\"
                            ";
        // line 102
        if (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "value", []) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_file_path_sync_auto\" />
                    <label for=\"wcml_file_path_sync_auto\">";
        // line 103
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "label_same", []), "html", null, true);
        echo "</label>
                </li>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"0\"
                            ";
        // line 107
        if (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "value", []) == 0)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_file_path_sync_self\" />
                    <label for=\"wcml_file_path_sync_self\">";
        // line 108
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "file_sync", []), "label_diff", []), "html", null, true);
        echo "</label>
                </li>
            </ul>


        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 121
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "product_reviews", []), "heading", []), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 122
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "product_reviews", []), "tip", []), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"reviews_in_all_languages\" value=\"1\"
                            ";
        // line 131
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "product_reviews", []), "reviews_in_all_languages", []), "value", []) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_reviews_in_all_languages\" />
                    <label for=\"wcml_reviews_in_all_languages\">";
        // line 132
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "product_reviews", []), "reviews_in_all_languages", []), "label", []), "html", null, true);
        echo "</label>
                </li>
            </ul>

        </div>

    </div>


    <div class=\"wcml-section cart-sync-section\">
        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 144
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "heading", []), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 145
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "tip", []), "html", null, true);
        echo "\"></i>
            </h3>
        </div>
        <div class=\"wcml-section-content\">

            ";
        // line 150
        if ( !$this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "wpml_cookie_enabled", [])) {
            // line 151
            echo "                <i class=\"otgs-ico-warning\"></i>
                <strong>";
            // line 152
            echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "cookie_not_enabled_message", []);
            echo "</strong>
            ";
        }
        // line 154
        echo "
            <div class=\"wcml-section-content-inner\">
                <h4>
                    ";
        // line 157
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "lang_switch", []), "heading", []), "html", null, true);
        echo "
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"";
        // line 161
        echo \WPML\Core\twig_escape_filter($this->env, ($context["wcml_cart_sync"] ?? null), "html", null, true);
        echo "\"
                                ";
        // line 162
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "lang_switch", []), "value", []) == ($context["wcml_cart_sync"] ?? null))) {
            echo " checked=\"checked\"";
        }
        // line 163
        echo "                                ";
        if ( !$this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "wpml_cookie_enabled", [])) {
            echo " disabled=\"disabled\"";
        }
        // line 164
        echo "                               id=\"wcml_cart_sync_lang_sync\" />
                        <label for=\"wcml_cart_sync_lang_sync\">";
        // line 165
        echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "lang_switch", []), "sync_label", []);
        echo "</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"";
        // line 168
        echo \WPML\Core\twig_escape_filter($this->env, ($context["wcml_cart_clear"] ?? null), "html", null, true);
        echo "\"
                                ";
        // line 169
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "lang_switch", []), "value", []) == ($context["wcml_cart_clear"] ?? null))) {
            echo " checked=\"checked\"";
        }
        // line 170
        echo "                                ";
        if ( !$this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "wpml_cookie_enabled", [])) {
            echo " disabled=\"disabled\"";
        }
        // line 171
        echo "                               id=\"wcml_cart_sync_lang_clear\" />
                        <label for=\"wcml_cart_sync_lang_clear\">";
        // line 172
        echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "lang_switch", []), "clear_label", []);
        echo "</label>
                    </li>
                </ul>
            </div>
            <div class=\"wcml-section-content-inner\">
                <h4>
                    ";
        // line 178
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "currency_switch", []), "heading", []), "html", null, true);
        echo "
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"";
        // line 182
        echo \WPML\Core\twig_escape_filter($this->env, ($context["wcml_cart_sync"] ?? null), "html", null, true);
        echo "\"
                                ";
        // line 183
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "currency_switch", []), "value", []) == ($context["wcml_cart_sync"] ?? null))) {
            echo " checked=\"checked\"";
        }
        // line 184
        echo "                                ";
        if ( !$this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "wpml_cookie_enabled", [])) {
            echo " disabled=\"disabled\"";
        }
        // line 185
        echo "                               id=\"wcml_cart_sync_curr_sync\" />
                        <label for=\"wcml_cart_sync_curr_sync\">";
        // line 186
        echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "currency_switch", []), "sync_label", []);
        echo "</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"";
        // line 189
        echo \WPML\Core\twig_escape_filter($this->env, ($context["wcml_cart_clear"] ?? null), "html", null, true);
        echo "\"
                                ";
        // line 190
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "currency_switch", []), "value", []) == ($context["wcml_cart_clear"] ?? null))) {
            echo " checked=\"checked\"";
        }
        // line 191
        echo "                                ";
        if ( !$this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "wpml_cookie_enabled", [])) {
            echo " disabled=\"disabled\"";
        }
        // line 192
        echo "                               id=\"wcml_cart_sync_curr_clear\" />
                        <label for=\"wcml_cart_sync_curr_clear\">";
        // line 193
        echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "currency_switch", []), "clear_label", []);
        echo "</label>
                    </li>
                </ul>
                <p>
                    ";
        // line 197
        echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "cart_sync", []), "doc_link", []);
        echo "
                </p>
            </div>
        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    ";
        // line 204
        echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('wp_do_action')->getCallable(), ["wcml_settings_ui_after_default"]), "html", null, true);
        echo "

    ";
        // line 206
        echo $this->getAttribute(($context["form"] ?? null), "nonce", []);
        echo "
    <p class=\"wpml-margin-top-sm\">
        <input type='submit' name=\"wcml_save_settings\" value='";
        // line 208
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "save_label", []), "html", null, true);
        echo "' class='button-primary'/>
    </p>
</form>
<a class=\"alignright\" href=\"";
        // line 211
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["troubleshooting"] ?? null), "url", []), "html", null, true);
        echo "\">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["troubleshooting"] ?? null), "label", []), "html", null, true);
        echo "</a>";
    }

    public function getTemplateName()
    {
        return "settings-ui.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  455 => 211,  449 => 208,  444 => 206,  439 => 204,  429 => 197,  422 => 193,  419 => 192,  414 => 191,  410 => 190,  406 => 189,  400 => 186,  397 => 185,  392 => 184,  388 => 183,  384 => 182,  377 => 178,  368 => 172,  365 => 171,  360 => 170,  356 => 169,  352 => 168,  346 => 165,  343 => 164,  338 => 163,  334 => 162,  330 => 161,  323 => 157,  318 => 154,  313 => 152,  310 => 151,  308 => 150,  300 => 145,  296 => 144,  281 => 132,  275 => 131,  263 => 122,  259 => 121,  243 => 108,  237 => 107,  230 => 103,  224 => 102,  212 => 93,  208 => 92,  192 => 79,  186 => 78,  174 => 69,  170 => 68,  155 => 56,  149 => 55,  142 => 51,  136 => 50,  124 => 41,  120 => 40,  114 => 36,  103 => 28,  97 => 27,  93 => 26,  87 => 23,  81 => 22,  77 => 21,  69 => 16,  65 => 15,  61 => 14,  57 => 13,  49 => 8,  45 => 7,  40 => 4,  38 => 3,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<form method=\"post\" action=\"{{ form.action }}\">

    {% if form.translation_interface %}
        <div class=\"wcml-section\">
            <div class=\"wcml-section-header\">
                <h3>
                    {{ form.translation_interface.heading }}
                    <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.translation_interface.tip }}\"></i>
                </h3>
            </div>
            <div class=\"wcml-section-content\">

                <div id=\"wcml-translation-interface-dialog-confirm\" title=\"{{ form.translation_interface.heading }}\" class=\"hidden\">
                    <p>{{ form.translation_interface.pb_warning }}</p>
                    <input type=\"hidden\" class=\"ok-button\" value=\"{{ form.translation_interface.pb_warning_ok_button }}\" />
                    <input type=\"hidden\" class=\"cancel-button\" value=\"{{ form.translation_interface.pb_warning_cancel_button }}\"/>
                </div>

                <ul>
                    <li>
                        <input type=\"radio\" name=\"trnsl_interface\" value=\"{{ wpml_translation }}\"
                                {% if form.translation_interface.controls_value == wpml_translation %} checked=\"checked\"{% endif %} id=\"wcml_trsl_interface_wcml\" />
                        <label for=\"wcml_trsl_interface_wcml\">{{ form.translation_interface.wcml.label }}</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"trnsl_interface\" value=\"{{ native_translation }}\"
                                {% if form.translation_interface.controls_value == native_translation %} checked=\"checked\"{% endif %} id=\"wcml_trsl_interface_native\" />
                        <label for=\"wcml_trsl_interface_native\">{{ form.translation_interface.native.label|raw }}</label>
                    </li>
                </ul>

            </div> <!-- .wcml-section-content -->

        </div> <!-- .wcml-section -->
    {% endif %}
    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                {{ form.synchronization.heading }}
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.synchronization.tip }}\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_date\" value=\"1\"
                            {% if form.synchronization.sync_date.value == 1 %} checked=\"checked\"{% endif %} id=\"wcml_products_sync_date\" />
                    <label for=\"wcml_products_sync_date\">{{ form.synchronization.sync_date.label }}</label>
                </li>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_order\" value=\"1\"
                            {% if form.synchronization.sync_order.value == 1 %} checked=\"checked\"{% endif %} id=\"wcml_products_sync_order\" />
                    <label for=\"wcml_products_sync_order\">{{ form.synchronization.sync_order.label }}</label>
                </li>
            </ul>

        </div>

    </div>

    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                {{ form.media_synchronization.heading }}
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.media_synchronization.tip }}\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"sync_media\" value=\"1\"
                            {% if form.media_synchronization.sync_media.value == 1 %} checked=\"checked\"{% endif %} id=\"wcml_sync_media\" />
                    <label for=\"wcml_sync_media\">{{ form.media_synchronization.sync_media.label }}</label>
                </li>
            </ul>

        </div>

    </div>


    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                {{ form.file_sync.heading }}
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.file_sync.tip }}\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"1\"
                            {% if form.file_sync.value == 1 %} checked=\"checked\"{% endif %} id=\"wcml_file_path_sync_auto\" />
                    <label for=\"wcml_file_path_sync_auto\">{{ form.file_sync.label_same }}</label>
                </li>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"0\"
                            {% if form.file_sync.value == 0 %} checked=\"checked\"{% endif %} id=\"wcml_file_path_sync_self\" />
                    <label for=\"wcml_file_path_sync_self\">{{ form.file_sync.label_diff }}</label>
                </li>
            </ul>


        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                {{ form.product_reviews.heading }}
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.product_reviews.tip }}\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"reviews_in_all_languages\" value=\"1\"
                            {% if form.product_reviews.reviews_in_all_languages.value == 1 %} checked=\"checked\"{% endif %} id=\"wcml_reviews_in_all_languages\" />
                    <label for=\"wcml_reviews_in_all_languages\">{{ form.product_reviews.reviews_in_all_languages.label }}</label>
                </li>
            </ul>

        </div>

    </div>


    <div class=\"wcml-section cart-sync-section\">
        <div class=\"wcml-section-header\">
            <h3>
                {{ form.cart_sync.heading }}
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.cart_sync.tip }}\"></i>
            </h3>
        </div>
        <div class=\"wcml-section-content\">

            {% if not form.cart_sync.wpml_cookie_enabled %}
                <i class=\"otgs-ico-warning\"></i>
                <strong>{{ form.cart_sync.cookie_not_enabled_message|raw }}</strong>
            {% endif %}

            <div class=\"wcml-section-content-inner\">
                <h4>
                    {{ form.cart_sync.lang_switch.heading }}
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"{{ wcml_cart_sync }}\"
                                {% if form.cart_sync.lang_switch.value == wcml_cart_sync %} checked=\"checked\"{% endif %}
                                {% if not form.cart_sync.wpml_cookie_enabled %} disabled=\"disabled\"{% endif %}
                               id=\"wcml_cart_sync_lang_sync\" />
                        <label for=\"wcml_cart_sync_lang_sync\">{{ form.cart_sync.lang_switch.sync_label|raw }}</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"{{ wcml_cart_clear }}\"
                                {% if form.cart_sync.lang_switch.value == wcml_cart_clear %} checked=\"checked\"{% endif %}
                                {% if not form.cart_sync.wpml_cookie_enabled %} disabled=\"disabled\"{% endif %}
                               id=\"wcml_cart_sync_lang_clear\" />
                        <label for=\"wcml_cart_sync_lang_clear\">{{ form.cart_sync.lang_switch.clear_label|raw }}</label>
                    </li>
                </ul>
            </div>
            <div class=\"wcml-section-content-inner\">
                <h4>
                    {{ form.cart_sync.currency_switch.heading }}
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"{{ wcml_cart_sync }}\"
                                {% if form.cart_sync.currency_switch.value == wcml_cart_sync %} checked=\"checked\"{% endif %}
                                {% if not form.cart_sync.wpml_cookie_enabled %} disabled=\"disabled\"{% endif %}
                               id=\"wcml_cart_sync_curr_sync\" />
                        <label for=\"wcml_cart_sync_curr_sync\">{{ form.cart_sync.currency_switch.sync_label|raw }}</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"{{ wcml_cart_clear }}\"
                                {% if form.cart_sync.currency_switch.value == wcml_cart_clear %} checked=\"checked\"{% endif %}
                                {% if not form.cart_sync.wpml_cookie_enabled %} disabled=\"disabled\"{% endif %}
                               id=\"wcml_cart_sync_curr_clear\" />
                        <label for=\"wcml_cart_sync_curr_clear\">{{ form.cart_sync.currency_switch.clear_label|raw }}</label>
                    </li>
                </ul>
                <p>
                    {{ form.cart_sync.doc_link|raw }}
                </p>
            </div>
        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    {{ wp_do_action( 'wcml_settings_ui_after_default' ) }}

    {{ form.nonce|raw }}
    <p class=\"wpml-margin-top-sm\">
        <input type='submit' name=\"wcml_save_settings\" value='{{ form.save_label }}' class='button-primary'/>
    </p>
</form>
<a class=\"alignright\" href=\"{{ troubleshooting.url }}\">{{ troubleshooting.label }}</a>", "settings-ui.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/settings-ui.twig");
    }
}
