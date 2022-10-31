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

/* pagination.twig */
class __TwigTemplate_712ff17aa5a806cd67605d6cee1d08d508e903b31ef4ced7c2c42d48412fcba3 extends \WPML\Core\Twig\Template
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
        if (($context["display"] ?? null)) {
            // line 2
            echo "\t<div class=\"tablenav bottom clearfix\">
\t\t<div class=\"tablenav-pages\">
\t\t\t<span class=\"displaying-num\">";
            // line 4
            echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "items", []), ($context["products_count"] ?? null)), "html", null, true);
            echo "</span>
\t\t\t<span class=\"pagination-links\">
\t\t\t\t";
            // line 6
            if (($context["show"] ?? null)) {
                // line 7
                echo "\t\t\t\t\t<a class=\"first-page ";
                if ((($context["pn"] ?? null) == 1)) {
                    echo " disabled ";
                }
                echo "\"
\t\t\t\t\t   href=\"";
                // line 8
                echo \WPML\Core\twig_escape_filter($this->env, ($context["pagination_first"] ?? null), "html", null, true);
                echo "\"
\t\t\t\t\t   title=\"";
                // line 9
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "first", []));
                echo "\">&laquo;</a>

\t\t\t\t\t<a class=\"prev-page ";
                // line 11
                if ((($context["pn"] ?? null) == 1)) {
                    echo " disabled ";
                }
                echo "\"
\t\t\t\t\t   href=\"";
                // line 12
                echo \WPML\Core\twig_escape_filter($this->env, ($context["pagination_prev"] ?? null), "html", null, true);
                echo "\"
\t\t\t\t\t   title=\"";
                // line 13
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "previous", []));
                echo "\">&lsaquo;</a>

\t\t\t\t\t<span class=\"paging-input\">
\t\t\t\t\t\t<label for=\"current-page-selector\" class=\"screen-reader-text\">
\t\t\t\t\t\t\t";
                // line 17
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "select", []), "html", null, true);
                echo "
\t\t\t\t\t\t</label>
\t\t\t\t\t\t<input class=\"current-page\" id=\"current-page-selector\"
\t\t\t\t\t\t\t   title=\"";
                // line 20
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "current", []));
                echo "\"
\t\t\t\t\t\t\t   type=\"text\" name=\"paged\" value=\"";
                // line 21
                echo \WPML\Core\twig_escape_filter($this->env, ($context["pn"] ?? null), "html", null, true);
                echo "\" size=\"2\">
\t\t\t\t\t\t&nbsp;";
                // line 22
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "of", []), "html", null, true);
                echo "&nbsp;
\t\t\t\t\t\t<span\tclass=\"total-pages\">";
                // line 23
                echo \WPML\Core\twig_escape_filter($this->env, ($context["last"] ?? null), "html", null, true);
                echo "</span>
\t\t\t\t\t</span>

\t\t\t\t\t<a class=\"next-page ";
                // line 26
                if ((($context["pn"] ?? null) == ($context["last"] ?? null))) {
                    echo " disabled ";
                }
                echo "\"
\t\t\t\t\t   href=\"";
                // line 27
                echo \WPML\Core\twig_escape_filter($this->env, ($context["pagination_next"] ?? null), "html", null, true);
                echo "\"
\t\t\t\t\t   title=\"";
                // line 28
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "next", []));
                echo "\">&rsaquo;</a>

\t\t\t\t\t<a class=\"last-page ";
                // line 30
                if ((($context["pn"] ?? null) == ($context["last"] ?? null))) {
                    echo " disabled ";
                }
                echo "\"
\t\t\t\t\t   href=\"";
                // line 31
                echo \WPML\Core\twig_escape_filter($this->env, ($context["pagination_last"] ?? null), "html", null, true);
                echo "\"
\t\t\t\t\t   title=\"";
                // line 32
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "last", []));
                echo "\">&raquo;</a>
\t\t\t\t";
            }
            // line 34
            echo "\t\t\t</span>
\t\t</div>
\t</div>
";
        }
    }

    public function getTemplateName()
    {
        return "pagination.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  132 => 34,  127 => 32,  123 => 31,  117 => 30,  112 => 28,  108 => 27,  102 => 26,  96 => 23,  92 => 22,  88 => 21,  84 => 20,  78 => 17,  71 => 13,  67 => 12,  61 => 11,  56 => 9,  52 => 8,  45 => 7,  43 => 6,  38 => 4,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% if display %}
\t<div class=\"tablenav bottom clearfix\">
\t\t<div class=\"tablenav-pages\">
\t\t\t<span class=\"displaying-num\">{{ strings.items|format( products_count ) }}</span>
\t\t\t<span class=\"pagination-links\">
\t\t\t\t{% if show %}
\t\t\t\t\t<a class=\"first-page {% if pn == 1 %} disabled {% endif %}\"
\t\t\t\t\t   href=\"{{ pagination_first }}\"
\t\t\t\t\t   title=\"{{ strings.first|e }}\">&laquo;</a>

\t\t\t\t\t<a class=\"prev-page {% if pn == 1 %} disabled {% endif %}\"
\t\t\t\t\t   href=\"{{ pagination_prev }}\"
\t\t\t\t\t   title=\"{{ strings.previous|e }}\">&lsaquo;</a>

\t\t\t\t\t<span class=\"paging-input\">
\t\t\t\t\t\t<label for=\"current-page-selector\" class=\"screen-reader-text\">
\t\t\t\t\t\t\t{{ strings.select }}
\t\t\t\t\t\t</label>
\t\t\t\t\t\t<input class=\"current-page\" id=\"current-page-selector\"
\t\t\t\t\t\t\t   title=\"{{ strings.current|e }}\"
\t\t\t\t\t\t\t   type=\"text\" name=\"paged\" value=\"{{ pn }}\" size=\"2\">
\t\t\t\t\t\t&nbsp;{{ strings.of }}&nbsp;
\t\t\t\t\t\t<span\tclass=\"total-pages\">{{ last }}</span>
\t\t\t\t\t</span>

\t\t\t\t\t<a class=\"next-page {% if pn == last %} disabled {% endif %}\"
\t\t\t\t\t   href=\"{{ pagination_next }}\"
\t\t\t\t\t   title=\"{{ strings.next|e }}\">&rsaquo;</a>

\t\t\t\t\t<a class=\"last-page {% if pn == last %} disabled {% endif %}\"
\t\t\t\t\t   href=\"{{ pagination_last }}\"
\t\t\t\t\t   title=\"{{ strings.last|e }}\">&raquo;</a>
\t\t\t\t{% endif %}
\t\t\t</span>
\t\t</div>
\t</div>
{% endif %}", "pagination.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/products-list/pagination.twig");
    }
}
