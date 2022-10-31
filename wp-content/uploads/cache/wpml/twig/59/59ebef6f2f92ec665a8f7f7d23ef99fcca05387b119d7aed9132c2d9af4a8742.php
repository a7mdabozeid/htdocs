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

/* conf-warn.twig */
class __TwigTemplate_c49fc0fead06f944c23589d8ef3c75aa02ebf8796861941e630292f31c65c43d extends \WPML\Core\Twig\Template
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
        if (( !twig_test_empty(($context["xml_config_errors"] ?? null)) ||  !twig_test_empty(($context["miss_slug_lang"] ?? null)))) {
            // line 2
            echo "    <div class=\"wcml-section\">
        <div class=\"wcml-section-header\">
            <h3>
                ";
            // line 5
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "conf", []), "html", null, true);
            echo "
            </h3>
        </div>
        <div class=\"wcml-section-content\">
            <ul class=\"wcml-status-list\">
                ";
            // line 10
            if ( !twig_test_empty(($context["miss_slug_lang"] ?? null))) {
                // line 11
                echo "                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        ";
                // line 13
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "base_not_trnsl", []), "html", null, true);
                echo "
                        <ul class=\"wcml-lang-list\">
                            ";
                // line 15
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["miss_slug_lang"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["miss_lang"]) {
                    // line 16
                    echo "                                <li>
                                    <span class=\"wpml-title-flag\">
                                        <img src=\"";
                    // line 18
                    echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_flag_url')->getCallable(), [$this->getAttribute($context["miss_lang"], "code", [])]), "html", null, true);
                    echo "\" alt=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["miss_lang"], "english_name", []));
                    echo "\" />
                                    </span>
                                    ";
                    // line 20
                    echo \WPML\Core\twig_escape_filter($this->env, \WPML\Core\twig_capitalize_string_filter($this->env, $this->getAttribute($context["miss_lang"], "display_name", [])), "html", null, true);
                    echo "
                                </li>
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['miss_lang'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 23
                echo "                        </ul>
                        <a class=\"button-secondary\" href=\"";
                // line 24
                echo \WPML\Core\twig_escape_filter($this->env, ($context["slugs_tab"] ?? null), "html", null, true);
                echo "\">
                            ";
                // line 25
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "trsl_urls", []), "html", null, true);
                echo "
                        </a>
                    </li>
                ";
            }
            // line 29
            echo "
                ";
            // line 30
            if ( !twig_test_empty(($context["xml_config_errors"] ?? null))) {
                // line 31
                echo "                    <li class=\"wcml_xml_config_warnings\">
                        <i class=\"otgs-ico-warning\"></i>
                        <strong>";
                // line 33
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "over_sett", []), "html", null, true);
                echo "</strong>
                        <p>
                            ";
                // line 35
                echo sprintf($this->getAttribute(($context["strings"] ?? null), "check_conf", []), $this->getAttribute(($context["strings"] ?? null), "cont_set", []));
                echo "
                        </p>
                        <ul>
                            ";
                // line 38
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["xml_config_errors"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                    // line 39
                    echo "                                <li>";
                    echo $context["error"];
                    echo "</li>
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 41
                echo "                        </ul>
                    </li>
                ";
            }
            // line 44
            echo "            </ul>
        </div>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "conf-warn.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 44,  129 => 41,  120 => 39,  116 => 38,  110 => 35,  105 => 33,  101 => 31,  99 => 30,  96 => 29,  89 => 25,  85 => 24,  82 => 23,  73 => 20,  66 => 18,  62 => 16,  58 => 15,  53 => 13,  49 => 11,  47 => 10,  39 => 5,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% if( ( xml_config_errors is not empty  ) or ( miss_slug_lang is not empty ) ) %}
    <div class=\"wcml-section\">
        <div class=\"wcml-section-header\">
            <h3>
                {{ strings.conf }}
            </h3>
        </div>
        <div class=\"wcml-section-content\">
            <ul class=\"wcml-status-list\">
                {% if miss_slug_lang is not empty %}
                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        {{ strings.base_not_trnsl }}
                        <ul class=\"wcml-lang-list\">
                            {% for miss_lang in miss_slug_lang %}
                                <li>
                                    <span class=\"wpml-title-flag\">
                                        <img src=\"{{ get_flag_url( miss_lang.code ) }}\" alt=\"{{ miss_lang.english_name|e }}\" />
                                    </span>
                                    {{ miss_lang.display_name|capitalize }}
                                </li>
                            {% endfor %}
                        </ul>
                        <a class=\"button-secondary\" href=\"{{ slugs_tab }}\">
                            {{ strings.trsl_urls }}
                        </a>
                    </li>
                {% endif %}

                {% if xml_config_errors is not empty %}
                    <li class=\"wcml_xml_config_warnings\">
                        <i class=\"otgs-ico-warning\"></i>
                        <strong>{{ strings.over_sett }}</strong>
                        <p>
                            {{ strings.check_conf|raw|format( strings.cont_set )|raw }}
                        </p>
                        <ul>
                            {% for error in xml_config_errors %}
                                <li>{{ error|raw }}</li>
                            {% endfor %}
                        </ul>
                    </li>
                {% endif %}
            </ul>
        </div>
    </div>
{% endif %}", "conf-warn.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/conf-warn.twig");
    }
}
