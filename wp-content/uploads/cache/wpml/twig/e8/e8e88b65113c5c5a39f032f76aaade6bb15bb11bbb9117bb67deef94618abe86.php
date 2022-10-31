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

/* multi_currencies.twig */
class __TwigTemplate_d51cd9f77012259c1bf02b5020b25081eb6c24543f5e6131e1345a814e8634a3 extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-section\">
    <div class=\"wcml-section-header\">
        <h3>
            ";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "mc_missing", []), "html", null, true);
        echo "
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list\">
            <li>
                ";
        // line 10
        if ( !($context["mc_enabled"] ?? null)) {
            // line 11
            echo "                    <i>";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_enabled", []), "html", null, true);
            echo "</i>
                ";
        } else {
            // line 13
            echo "                    ";
            if (twig_test_empty(($context["sec_currencies"] ?? null))) {
                // line 14
                echo "                        <i class=\"otgs-ico-warning\"></i>
                        ";
                // line 15
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "no_secondary", []), "html", null, true);
                echo "

                        <p>
                            <a class=\"button-secondary aligncenter\" href=\"";
                // line 18
                echo \WPML\Core\twig_escape_filter($this->env, ($context["add_cur_link"] ?? null), "html", null, true);
                echo "\">
                                ";
                // line 19
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "add_cur", []), "html", null, true);
                echo "
                            </a>
                        </p>

                    ";
            } else {
                // line 24
                echo "                        <i class=\"otgs-ico-ok\"></i>
                        ";
                // line 25
                echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "sec_currencies", []), ($context["sec_currencies"] ?? null)), "html", null, true);
                echo "
                    ";
            }
            // line 27
            echo "                ";
        }
        // line 28
        echo "            </li>
        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "multi_currencies.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  89 => 28,  86 => 27,  81 => 25,  78 => 24,  70 => 19,  66 => 18,  60 => 15,  57 => 14,  54 => 13,  48 => 11,  46 => 10,  37 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-section\">
    <div class=\"wcml-section-header\">
        <h3>
            {{ strings.mc_missing }}
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list\">
            <li>
                {% if not mc_enabled %}
                    <i>{{ strings.not_enabled }}</i>
                {% else %}
                    {% if sec_currencies is empty %}
                        <i class=\"otgs-ico-warning\"></i>
                        {{ strings.no_secondary }}

                        <p>
                            <a class=\"button-secondary aligncenter\" href=\"{{ add_cur_link }}\">
                                {{ strings.add_cur }}
                            </a>
                        </p>

                    {% else %}
                        <i class=\"otgs-ico-ok\"></i>
                        {{ strings.sec_currencies|format( sec_currencies ) }}
                    {% endif %}
                {% endif %}
            </li>
        </ul>
    </div>
</div>", "multi_currencies.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/multi_currencies.twig");
    }
}
