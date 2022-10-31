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

/* currency-switcher-options-dialog.twig */
class __TwigTemplate_377740dcf010419fd6bf385b2e4786b775bc3c66e347a3015dfccfccb45223fb extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-dialog hidden\" id=\"wcml_currency_switcher_options_";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "currency_switcher", []), "html", null, true);
        echo "\" title=\"";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "title", []), "html", null, true);
        echo "\">

    <div id=\"wcml_currency_switcher_options_form_";
        // line 3
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "currency_switcher", []), "html", null, true);
        echo "\" class=\"wcml-currency-switcher-options-form\">

        <div id=\"wcml_curr_sel_preview_wrap\" class=\"wcml-currency-preview-wrapper\">
            <strong class=\"wcml-currency-preview-label\">";
        // line 6
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "preview", []), "html", null, true);
        echo "</strong>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_preview_nonce\" value=\"";
        // line 7
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "preview_nonce", []), "html", null, true);
        echo "\"/>
            <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview ";
        // line 8
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "currency_switcher", []), "html", null, true);
        echo "\">
                ";
        // line 9
        echo $this->getAttribute(($context["form"] ?? null), "switcher_preview", []);
        echo "
            </div>
        </div>

        <div id=\"wcml_curr_options_wrap\" class=\"wcml-currency-switcher-options\">
            ";
        // line 14
        if (($this->getAttribute(($context["args"] ?? null), "currency_switcher", []) == "new_widget")) {
            // line 15
            echo "
                    <h4>";
            // line 16
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "widgets", []), "widget_area", []), "html", null, true);
            echo "</h4>
                    <select id=\"wcml-cs-widget\">
                        <option selected disabled>";
            // line 18
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "widgets", []), "choose_label", []), "html", null, true);
            echo "</option>
                        ";
            // line 19
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["form"] ?? null), "widgets", []), "available_sidebars", []));
            foreach ($context['_seq'] as $context["_key"] => $context["sidebar"]) {
                // line 20
                echo "                            <option value=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["sidebar"], "id", [], "array"), "html", null, true);
                echo "\">";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["sidebar"], "name", [], "array"), "html", null, true);
                echo "</option>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sidebar'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 22
            echo "                    </select>

            ";
        }
        // line 25
        echo "
            <h4>";
        // line 26
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "switcher_style", []), "label", []), "html", null, true);
        echo "</h4>
            <ul class=\"wcml_curr_style\">
                <li>
                    <label>
                        <select id=\"currency_switcher_style\">
                            <optgroup label=\"";
        // line 31
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "switcher_style", []), "core", []), "html", null, true);
        echo "\">
                                ";
        // line 32
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["args"] ?? null), "switcher_templates", []), "core", [], "array"));
        foreach ($context['_seq'] as $context["switcher_template_id"] => $context["switcher_template"]) {
            // line 33
            echo "                                    <option value=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $context["switcher_template_id"], "html", null, true);
            echo "\" ";
            if (($this->getAttribute(($context["args"] ?? null), "switcher_style", []) == $context["switcher_template_id"])) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["switcher_template"], "name", [], "array"), "html", null, true);
            echo "</option>
                                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['switcher_template_id'], $context['switcher_template'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "                            </optgroup>
                            <optgroup label=\"";
        // line 36
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "switcher_style", []), "custom", []), "html", null, true);
        echo "\">
                                ";
        // line 37
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["args"] ?? null), "switcher_templates", []), "custom", [], "array"));
        foreach ($context['_seq'] as $context["switcher_template_id"] => $context["switcher_template"]) {
            // line 38
            echo "                                    <option value=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $context["switcher_template_id"], "html", null, true);
            echo "\" ";
            if (($this->getAttribute(($context["args"] ?? null), "switcher_style", []) == $context["switcher_template_id"])) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["switcher_template"], "name", [], "array"), "html", null, true);
            echo "</option>
                                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['switcher_template_id'], $context['switcher_template'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "                            </optgroup>
                        </select>
                    </label>
                </li>
            </ul>

            <h4>";
        // line 46
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "template", []), "label", []), "html", null, true);
        echo "</h4>
            <input type=\"text\" name=\"wcml_curr_template\" size=\"50\" value=\"";
        // line 47
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "template", []), "html", null, true);
        echo "\"/>
            <p class=\"explanation-text\">
                <span class=\"display-block\">";
        // line 49
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "template", []), "template_tip", []), "html", null, true);
        echo "</span>
                <span class=\"display-block\">";
        // line 50
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "template", []), "parameters", []), "html", null, true);
        echo ": ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "template", []), "parameters_list", []), "html", null, true);
        echo "</span>
                <span class=\"display-block js-toggle-cs-style\" ";
        // line 51
        if (($this->getAttribute(($context["args"] ?? null), "style", []) != "list")) {
            echo "style=\"display: none;\"";
        }
        echo ">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "switcher_style", []), "allowed_tags", []), "html", null, true);
        echo "</span>
            </p>
            <input type=\"hidden\" id=\"currency_switcher_default\" value=\"";
        // line 53
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "template_default", []), "html", null, true);
        echo "\"/>

            ";
        // line 55
        if (($this->getAttribute(($context["args"] ?? null), "currency_switcher", []) != "product")) {
            // line 56
            echo "                <h4>";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "widgets", []), "widget_title", []), "html", null, true);
            echo "</h4>
                <input type=\"text\" name=\"wcml_cs_widget_title\" size=\"50\" value=\"";
            // line 57
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "widget_title", []), "html", null, true);
            echo "\"/>
            ";
        }
        // line 59
        echo "
            <div class=\"js-wcml-cs-panel-colors wcml-cs-panel-colors\">
                <h4>";
        // line 61
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "colors", []), "label", []), "html", null, true);
        echo "</h4>

                <label for=\"wcml-cs-";
        // line 63
        echo \WPML\Core\twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "-colorpicker-preset\">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "colors", []), "theme", []), "html", null, true);
        echo "</label>
                <select name=\"wcml-cs-";
        // line 64
        echo \WPML\Core\twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "-colorpicker-preset\" class=\"js-wcml-cs-colorpicker-preset\">
                    <option selected disabled>-- ";
        // line 65
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "colors", []), "select_option_choose", []), "html", null, true);
        echo " --</option>
                    ";
        // line 66
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["color_schemes"] ?? null));
        foreach ($context['_seq'] as $context["scheme_id"] => $context["scheme"]) {
            // line 67
            echo "                        <option value=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $context["scheme_id"], "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["scheme"], "label", []), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scheme_id'], $context['scheme'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "                </select>

                <div>
                    <table>
                        <tr>
                            <td>
                            </td>
                            <th>";
        // line 76
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "colors", []), "normal", []), "html", null, true);
        echo "</th>
                            <th>";
        // line 77
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "colors", []), "hover", []), "html", null, true);
        echo "</th>
                        </tr>
                        ";
        // line 79
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["options"] ?? null));
        foreach ($context['_seq'] as $context["option_id"] => $context["option"]) {
            // line 80
            echo "                            <tr>
                                <td>";
            // line 81
            echo \WPML\Core\twig_escape_filter($this->env, $context["option"], "html", null, true);
            echo "</td>
                                ";
            // line 82
            if ( !(null === $this->getAttribute($this->getAttribute(($context["args"] ?? null), "options", []), ($context["option_id"] . "_normal"), [], "array"))) {
                // line 83
                echo "                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-";
                // line 84
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_normal\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-";
                // line 85
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "-normal\" name=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_normal\"
                                               value=\"";
                // line 86
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["args"] ?? null), "options", []), ($context["option_id"] . "_normal"), [], "array"), "html", null, true);
                echo "\" style=\"\">
                                    </td>
                                ";
            }
            // line 89
            echo "                                ";
            if ( !(null === $this->getAttribute($this->getAttribute(($context["args"] ?? null), "options", []), ($context["option_id"] . "_hover"), [], "array"))) {
                // line 90
                echo "                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-";
                // line 91
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_hover\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-";
                // line 92
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "-hover\" name=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_hover\"
                                               value=\"";
                // line 93
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["args"] ?? null), "options", []), ($context["option_id"] . "_hover"), [], "array"), "html", null, true);
                echo "\" style=\"\">
                                    </td>
                                ";
            }
            // line 96
            echo "                            </tr>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['option_id'], $context['option'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 98
        echo "                    </table>

                </div>
            </div>
        </div>

    </div>
        <footer class=\"wpml-dialog-footer\">
            <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button alignleft\" value=\"";
        // line 106
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "cancel", []), "html", null, true);
        echo "\"/>&nbsp;
            <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary currency_switcher_save alignright\"
                   value=\"";
        // line 108
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "save", []), "html", null, true);
        echo "\" data-switcher=\"";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "current_switcher", []), "html", null, true);
        echo "\" data-stay=\"1\" />
            <input type=\"hidden\" id=\"wcml_currencies_switcher_save_settings_nonce\" value=\"";
        // line 109
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "save_settings_nonce", []), "html", null, true);
        echo "\"/>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_id\" value=\"";
        // line 110
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["args"] ?? null), "currency_switcher", []), "html", null, true);
        echo "\"/>
        </footer>

    </div>

";
    }

    public function getTemplateName()
    {
        return "currency-switcher-options-dialog.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  355 => 110,  351 => 109,  345 => 108,  340 => 106,  330 => 98,  323 => 96,  317 => 93,  311 => 92,  307 => 91,  304 => 90,  301 => 89,  295 => 86,  289 => 85,  285 => 84,  282 => 83,  280 => 82,  276 => 81,  273 => 80,  269 => 79,  264 => 77,  260 => 76,  251 => 69,  240 => 67,  236 => 66,  232 => 65,  228 => 64,  222 => 63,  217 => 61,  213 => 59,  208 => 57,  203 => 56,  201 => 55,  196 => 53,  187 => 51,  181 => 50,  177 => 49,  172 => 47,  168 => 46,  160 => 40,  145 => 38,  141 => 37,  137 => 36,  134 => 35,  119 => 33,  115 => 32,  111 => 31,  103 => 26,  100 => 25,  95 => 22,  84 => 20,  80 => 19,  76 => 18,  71 => 16,  68 => 15,  66 => 14,  58 => 9,  54 => 8,  50 => 7,  46 => 6,  40 => 3,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-dialog hidden\" id=\"wcml_currency_switcher_options_{{ args.currency_switcher }}\" title=\"{{ args.title }}\">

    <div id=\"wcml_currency_switcher_options_form_{{ args.currency_switcher }}\" class=\"wcml-currency-switcher-options-form\">

        <div id=\"wcml_curr_sel_preview_wrap\" class=\"wcml-currency-preview-wrapper\">
            <strong class=\"wcml-currency-preview-label\">{{ form.preview }}</strong>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_preview_nonce\" value=\"{{ form.preview_nonce }}\"/>
            <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview {{ args.currency_switcher }}\">
                {{ form.switcher_preview|raw }}
            </div>
        </div>

        <div id=\"wcml_curr_options_wrap\" class=\"wcml-currency-switcher-options\">
            {% if args.currency_switcher == 'new_widget' %}

                    <h4>{{ form.widgets.widget_area }}</h4>
                    <select id=\"wcml-cs-widget\">
                        <option selected disabled>{{ form.widgets.choose_label }}</option>
                        {% for sidebar in form.widgets.available_sidebars %}
                            <option value=\"{{ sidebar['id'] }}\">{{ sidebar['name'] }}</option>
                        {% endfor %}
                    </select>

            {% endif %}

            <h4>{{ form.switcher_style.label }}</h4>
            <ul class=\"wcml_curr_style\">
                <li>
                    <label>
                        <select id=\"currency_switcher_style\">
                            <optgroup label=\"{{ form.switcher_style.core }}\">
                                {% for switcher_template_id,switcher_template in args.switcher_templates['core'] %}
                                    <option value=\"{{ switcher_template_id }}\" {% if( args.switcher_style == switcher_template_id) %}selected=\"selected\"{% endif %}>{{ switcher_template['name'] }}</option>
                                {% endfor %}
                            </optgroup>
                            <optgroup label=\"{{ form.switcher_style.custom }}\">
                                {% for switcher_template_id,switcher_template in args.switcher_templates['custom'] %}
                                    <option value=\"{{ switcher_template_id }}\" {% if( args.switcher_style == switcher_template_id) %}selected=\"selected\"{% endif %}>{{ switcher_template['name'] }}</option>
                                {% endfor %}
                            </optgroup>
                        </select>
                    </label>
                </li>
            </ul>

            <h4>{{ form.template.label }}</h4>
            <input type=\"text\" name=\"wcml_curr_template\" size=\"50\" value=\"{{ args.template }}\"/>
            <p class=\"explanation-text\">
                <span class=\"display-block\">{{ form.template.template_tip }}</span>
                <span class=\"display-block\">{{ form.template.parameters }}: {{  form.template.parameters_list }}</span>
                <span class=\"display-block js-toggle-cs-style\" {% if(args.style != 'list') %}style=\"display: none;\"{% endif %}>{{ form.switcher_style.allowed_tags }}</span>
            </p>
            <input type=\"hidden\" id=\"currency_switcher_default\" value=\"{{ args.template_default }}\"/>

            {% if args.currency_switcher != 'product' %}
                <h4>{{ form.widgets.widget_title }}</h4>
                <input type=\"text\" name=\"wcml_cs_widget_title\" size=\"50\" value=\"{{ args.widget_title }}\"/>
            {% endif %}

            <div class=\"js-wcml-cs-panel-colors wcml-cs-panel-colors\">
                <h4>{{ form.colors.label }}</h4>

                <label for=\"wcml-cs-{{ id }}-colorpicker-preset\">{{ form.colors.theme }}</label>
                <select name=\"wcml-cs-{{ id }}-colorpicker-preset\" class=\"js-wcml-cs-colorpicker-preset\">
                    <option selected disabled>-- {{ form.colors.select_option_choose }} --</option>
                    {% for scheme_id, scheme in color_schemes %}
                        <option value=\"{{ scheme_id }}\">{{ scheme.label }}</option>
                    {% endfor %}
                </select>

                <div>
                    <table>
                        <tr>
                            <td>
                            </td>
                            <th>{{ form.colors.normal }}</th>
                            <th>{{ form.colors.hover }}</th>
                        </tr>
                        {% for option_id, option in options %}
                            <tr>
                                <td>{{ option }}</td>
                                {% if args.options[ option_id ~ '_normal' ] is not null %}
                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-{{ option_id }}_normal\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-{{ option_id }}-normal\" name=\"{{ option_id }}_normal\"
                                               value=\"{{ args.options[ option_id ~ '_normal' ] }}\" style=\"\">
                                    </td>
                                {% endif %}
                                {% if args.options[ option_id ~ '_hover' ] is not null %}
                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-{{ option_id }}_hover\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-{{ option_id }}-hover\" name=\"{{ option_id }}_hover\"
                                               value=\"{{ args.options[ option_id ~ '_hover' ] }}\" style=\"\">
                                    </td>
                                {% endif %}
                            </tr>
                        {% endfor %}
                    </table>

                </div>
            </div>
        </div>

    </div>
        <footer class=\"wpml-dialog-footer\">
            <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button alignleft\" value=\"{{ form.cancel }}\"/>&nbsp;
            <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary currency_switcher_save alignright\"
                   value=\"{{ form.save }}\" data-switcher=\"{{ args.current_switcher }}\" data-stay=\"1\" />
            <input type=\"hidden\" id=\"wcml_currencies_switcher_save_settings_nonce\" value=\"{{ form.save_settings_nonce }}\"/>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_id\" value=\"{{ args.currency_switcher }}\"/>
        </footer>

    </div>

", "currency-switcher-options-dialog.twig", "/home/alshia6/public_html/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/currency-switcher-options-dialog.twig");
    }
}
