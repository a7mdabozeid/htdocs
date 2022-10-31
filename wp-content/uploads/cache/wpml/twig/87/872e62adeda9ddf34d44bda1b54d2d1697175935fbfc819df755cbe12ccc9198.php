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

/* plugins-status.twig */
class __TwigTemplate_87de28647b7c47fa42f8ca4853003c3304686be1fc854d3128512984762d9674 extends \WPML\Core\Twig\Template
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
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "status", []), "html", null, true);
        echo "
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 5
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "depends", []));
        echo "\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list wcml-plugins-status-list\">
            ";
        // line 10
        if (($context["icl_version"] ?? null)) {
            // line 11
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 13
            echo sprintf($this->getAttribute(($context["strings"] ?? null), "inst_active", []), $this->getAttribute(($context["strings"] ?? null), "wpml", []));
            echo "
                </li>
                ";
            // line 15
            if (($context["icl_setup"] ?? null)) {
                // line 16
                echo "                    <li>
                        <i class=\"otgs-ico-ok\"></i>
                        ";
                // line 18
                echo sprintf($this->getAttribute(($context["strings"] ?? null), "is_setup", []), $this->getAttribute(($context["strings"] ?? null), "wpml", []));
                echo "
                    </li>
                ";
            } else {
                // line 21
                echo "                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        ";
                // line 23
                echo sprintf($this->getAttribute(($context["strings"] ?? null), "not_setup", []), $this->getAttribute(($context["strings"] ?? null), "wpml", []));
                echo "
                    </li>
                ";
            }
            // line 26
            echo "            ";
        }
        // line 27
        echo "            ";
        if (($context["tm_version"] ?? null)) {
            // line 28
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 30
            echo sprintf($this->getAttribute(($context["strings"] ?? null), "inst_active", []), $this->getAttribute(($context["strings"] ?? null), "tm", []));
            echo "
                </li>
            ";
        }
        // line 33
        echo "            ";
        if (($context["st_version"] ?? null)) {
            // line 34
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 36
            echo sprintf($this->getAttribute(($context["strings"] ?? null), "inst_active", []), $this->getAttribute(($context["strings"] ?? null), "st", []));
            echo "
                </li>
            ";
        }
        // line 39
        echo "            ";
        if (($context["wc"] ?? null)) {
            // line 40
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 42
            echo sprintf($this->getAttribute(($context["strings"] ?? null), "inst_active", []), $this->getAttribute(($context["strings"] ?? null), "wc", []));
            echo "
                </li>
            ";
        }
        // line 45
        echo "        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "plugins-status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 45,  118 => 42,  114 => 40,  111 => 39,  105 => 36,  101 => 34,  98 => 33,  92 => 30,  88 => 28,  85 => 27,  82 => 26,  76 => 23,  72 => 21,  66 => 18,  62 => 16,  60 => 15,  55 => 13,  51 => 11,  49 => 10,  41 => 5,  37 => 4,  32 => 1,);
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
            {{ strings.status }}
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ strings.depends|e }}\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list wcml-plugins-status-list\">
            {% if icl_version %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.inst_active|format( strings.wpml )|raw }}
                </li>
                {% if icl_setup %}
                    <li>
                        <i class=\"otgs-ico-ok\"></i>
                        {{ strings.is_setup|format( strings.wpml )|raw }}
                    </li>
                {% else %}
                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        {{ strings.not_setup|format( strings.wpml )|raw }}
                    </li>
                {% endif %}
            {% endif %}
            {% if tm_version %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.inst_active|format( strings.tm )|raw }}
                </li>
            {% endif %}
            {% if st_version %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.inst_active|format( strings.st )|raw }}
                </li>
            {% endif %}
            {% if wc %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.inst_active|format( strings.wc )|raw }}
                </li>
            {% endif %}
        </ul>
    </div>
</div>", "plugins-status.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/plugins-status.twig");
    }
}
