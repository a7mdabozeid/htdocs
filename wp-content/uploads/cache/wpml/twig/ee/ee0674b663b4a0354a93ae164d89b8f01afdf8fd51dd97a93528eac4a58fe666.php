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

/* multi-currency.twig */
class __TwigTemplate_8b51e9f98af910fd6d397b1b2c95b7138245bcdb0587b3412efcf7db7ebb9571 extends \WPML\Core\Twig\Template
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
        echo "
";
        // line 2
        echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('wp_do_action')->getCallable(), ["wcml_before_multi_currency_ui"]), "html", null, true);
        echo "


<form method=\"post\" action=\"";
        // line 5
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "action", []), "html", null, true);
        echo "\" id=\"wcml_mc_options\">
    ";
        // line 6
        echo $this->getAttribute(($context["form"] ?? null), "nonce", []);
        echo "
    <input type=\"hidden\" id=\"wcml_save_currency_nonce\" value=\"";
        // line 7
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "save_currency_nonce", []), "html", null, true);
        echo "\"/>
    <input type=\"hidden\" id=\"del_currency_nonce\" value=\"";
        // line 8
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "del_currency_nonce", []), "html", null, true);
        echo "\" />
    <input type=\"hidden\" id=\"currencies_list_nonce\" value=\"";
        // line 9
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "currencies_list_nonce", []), "html", null, true);
        echo "\" />
    <input type=\"hidden\" name=\"action\" value=\"save-mc-options\" />

    <div class=\"wcml-section \">
        <div class=\"wcml-section-header\">
            <h3>";
        // line 14
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "headers", []), "enable_disable", []), "html", null, true);
        echo "</h3>
        </div>
        <div class=\"wcml-section-content wcml-section-content-wide\">
            <p>
                <input type=\"checkbox\" name=\"multi_currency\" id=\"multi_currency_independent\" value=\"";
        // line 18
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "multi_currency_option", []), "html", null, true);
        echo "\"
                    ";
        // line 19
        if (($context["multi_currency_on"] ?? null)) {
            echo "checked=\"checked\"";
        }
        echo " ";
        if (($context["mco_disabled"] ?? null)) {
            echo "disabled=\"disabled\"";
        }
        echo " />
                <label for=\"multi_currency_independent\">
                    ";
        // line 21
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "label_mco", []), "html", null, true);
        echo "
                    &nbsp;
                    <a href=\"";
        // line 23
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "label_mco_learn_url", []), "html", null, true);
        echo "\" class=\"wpml-external-link\" target=\"_blank\">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "label_mco_learn_txt", []), "html", null, true);
        echo "</a>
                </label>
            </p>

            ";
        // line 27
        if (twig_test_empty(($context["wc_currency"] ?? null))) {
            // line 28
            echo "            <p>
                <i class=\"icon-warning-sign\"></i>
                ";
            // line 30
            echo ($context["wc_currency_empty_warn"] ?? null);
            echo "
            </p>
            ";
        }
        // line 33
        echo "
        </div>
    </div>

    ";
        // line 37
        if (($context["wc_currency"] ?? null)) {
            // line 38
            echo "    <div class=\"wcml-section\" id=\"multi-currency-per-language-details\" ";
            if (($this->getAttribute(($context["wcml_settings"] ?? null), "enable_multi_currency", []) != $this->getAttribute(($context["form"] ?? null), "multi_currency_option", []))) {
                echo "style=\"display:none\"";
            }
            echo ">

        <div class=\"wcml-section-header\">
            <h3>";
            // line 41
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "headers", []), "currencies", []), "html", null, true);
            echo "</h3>
        </div>

        <div id=\"wcml-multicurrency-options\" class=\"js-wcml-react-ui wcml-section-content wcml-section-content-wide\"></div>

        <ul id=\"display_custom_prices_select\" ";
            // line 46
            if (twig_test_empty(($context["multi_currency_on"] ?? null))) {
                echo "style=\"display: none;\"";
            }
            echo ">
            <li>
                <input type=\"checkbox\" name=\"display_custom_prices\" id=\"display_custom_prices\"
                   value=\"1\" ";
            // line 49
            if ($this->getAttribute($this->getAttribute(($context["form"] ?? null), "custom_prices_select", []), "checked", [])) {
                echo " checked=\"checked\"";
            }
            echo ">
                <label for=\"display_custom_prices\">";
            // line 50
            echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "custom_prices_select", []), "label", []);
            echo "</label>
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
            // line 51
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "custom_prices_select", []), "tip", []), "html", null, true);
            echo "\"></i>
            </li>
        </ul>

    </div>

    ";
            // line 57
            $this->loadTemplate("exchange-rates.twig", "multi-currency.twig", 57)->display(twig_array_merge($context, ($context["exchange_rates"] ?? null)));
            // line 58
            echo "
    ";
            // line 59
            echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('wp_do_action')->getCallable(), ["wcml_before_currency_switcher_options"]), "html", null, true);
            echo "

    ";
            // line 61
            $this->loadTemplate("currency-switcher-options.twig", "multi-currency.twig", 61)->display($context);
            // line 62
            echo "
    <input type=\"hidden\" id=\"wcml_warn_message\" value=\"";
            // line 63
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "navigate_warn", []), "html", null, true);
            echo "\" />
    <input type=\"hidden\" id=\"wcml_warn_disable_language_massage\" value=\"";
            // line 64
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "cur_lang_warn", []), "html", null, true);
            echo "\" />

    <p class=\"wpml-margin-top-sm\">
        <input id=\"wcml_mc_options_submit\" type='submit' value='";
            // line 67
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "submit", []), "html", null, true);
            echo "' class='button-primary'/>
    </p>

    ";
        }
        // line 71
        echo "
</form>
";
    }

    public function getTemplateName()
    {
        return "multi-currency.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  197 => 71,  190 => 67,  184 => 64,  180 => 63,  177 => 62,  175 => 61,  170 => 59,  167 => 58,  165 => 57,  156 => 51,  152 => 50,  146 => 49,  138 => 46,  130 => 41,  121 => 38,  119 => 37,  113 => 33,  107 => 30,  103 => 28,  101 => 27,  92 => 23,  87 => 21,  76 => 19,  72 => 18,  65 => 14,  57 => 9,  53 => 8,  49 => 7,  45 => 6,  41 => 5,  35 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("
{{ wp_do_action( 'wcml_before_multi_currency_ui' ) }}


<form method=\"post\" action=\"{{ form.action }}\" id=\"wcml_mc_options\">
    {{ form.nonce|raw }}
    <input type=\"hidden\" id=\"wcml_save_currency_nonce\" value=\"{{ form.save_currency_nonce }}\"/>
    <input type=\"hidden\" id=\"del_currency_nonce\" value=\"{{ form.del_currency_nonce }}\" />
    <input type=\"hidden\" id=\"currencies_list_nonce\" value=\"{{ form.currencies_list_nonce }}\" />
    <input type=\"hidden\" name=\"action\" value=\"save-mc-options\" />

    <div class=\"wcml-section \">
        <div class=\"wcml-section-header\">
            <h3>{{ strings.headers.enable_disable }}</h3>
        </div>
        <div class=\"wcml-section-content wcml-section-content-wide\">
            <p>
                <input type=\"checkbox\" name=\"multi_currency\" id=\"multi_currency_independent\" value=\"{{ form.multi_currency_option }}\"
                    {% if multi_currency_on %}checked=\"checked\"{% endif %} {% if mco_disabled %}disabled=\"disabled\"{% endif %} />
                <label for=\"multi_currency_independent\">
                    {{ form.label_mco }}
                    &nbsp;
                    <a href=\"{{ form.label_mco_learn_url }}\" class=\"wpml-external-link\" target=\"_blank\">{{ form.label_mco_learn_txt }}</a>
                </label>
            </p>

            {% if wc_currency is empty %}
            <p>
                <i class=\"icon-warning-sign\"></i>
                {{ wc_currency_empty_warn|raw }}
            </p>
            {% endif %}

        </div>
    </div>

    {% if wc_currency %}
    <div class=\"wcml-section\" id=\"multi-currency-per-language-details\" {% if wcml_settings.enable_multi_currency != form.multi_currency_option %}style=\"display:none\"{% endif %}>

        <div class=\"wcml-section-header\">
            <h3>{{ strings.headers.currencies }}</h3>
        </div>

        <div id=\"wcml-multicurrency-options\" class=\"js-wcml-react-ui wcml-section-content wcml-section-content-wide\"></div>

        <ul id=\"display_custom_prices_select\" {% if multi_currency_on is empty %}style=\"display: none;\"{% endif %}>
            <li>
                <input type=\"checkbox\" name=\"display_custom_prices\" id=\"display_custom_prices\"
                   value=\"1\" {% if form.custom_prices_select.checked %} checked=\"checked\"{% endif %}>
                <label for=\"display_custom_prices\">{{ form.custom_prices_select.label|raw }}</label>
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ form.custom_prices_select.tip }}\"></i>
            </li>
        </ul>

    </div>

    {% include 'exchange-rates.twig' with exchange_rates %}

    {{ wp_do_action( 'wcml_before_currency_switcher_options' ) }}

    {% include 'currency-switcher-options.twig' %}

    <input type=\"hidden\" id=\"wcml_warn_message\" value=\"{{ form.navigate_warn }}\" />
    <input type=\"hidden\" id=\"wcml_warn_disable_language_massage\" value=\"{{ form.cur_lang_warn }}\" />

    <p class=\"wpml-margin-top-sm\">
        <input id=\"wcml_mc_options_submit\" type='submit' value='{{ form.submit }}' class='button-primary'/>
    </p>

    {% endif %}

</form>
", "multi-currency.twig", "/home/alshia6/public_html/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/multi-currency.twig");
    }
}
