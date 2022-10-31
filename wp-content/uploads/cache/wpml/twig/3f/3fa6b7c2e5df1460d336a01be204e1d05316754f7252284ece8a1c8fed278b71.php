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

/* filter.twig */
class __TwigTemplate_d015702a041b1c7ae615456e59805cd68c4a4a2895d102e280b481873b047572 extends \WPML\Core\Twig\Template
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
            echo "\t<div class=\"tablenav top clearfix wcml-product-translation-filtering\">
\t\t<div class=\"alignleft\">
\t\t\t<select class=\"wcml_translation_status_lang\">
\t\t\t\t<option value=\"all\" ";
            // line 5
            if ( !($context["slang"] ?? null)) {
                echo " selected=\"selected\" ";
            }
            echo ">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "all_lang", []), "html", null, true);
            echo "</option>
\t\t\t\t";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["active_languages"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 7
                echo "\t\t\t\t\t<option\tvalue=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []));
                echo "\" ";
                if ((($context["slang"] ?? null) == $this->getAttribute($context["language"], "code", []))) {
                    echo " selected=\"selected\" ";
                }
                echo " >
\t\t\t\t\t\t";
                // line 8
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "display_name", []), "html", null, true);
                echo "
\t\t\t\t\t</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo "\t\t\t</select>

\t\t\t<select class=\"wcml_product_category\">
\t\t\t\t<option value=\"0\">";
            // line 14
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "all_cats", []), "html", null, true);
            echo "</option>
\t\t\t\t";
            // line 15
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["categories"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 16
                echo "\t\t\t\t\t<option value=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["category"], "term_taxonomy_id", []));
                echo "\" ";
                if ((($context["category_from_filter"] ?? null) == $this->getAttribute($context["category"], "term_taxonomy_id", []))) {
                    echo " selected=\"selected\" ";
                }
                echo ">
\t\t\t\t\t\t";
                // line 17
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["category"], "name", []), "html", null, true);
                echo "
\t\t\t\t\t</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "\t\t\t</select>

\t\t\t<select class=\"wcml_translation_status\">
\t\t\t\t<option value=\"all\">";
            // line 23
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "all_trnsl_stats", []), "html", null, true);
            echo "</option>
\t\t\t\t<option value=\"not\" ";
            // line 24
            if ((($context["trst"] ?? null) == "not")) {
                echo " selected=\"selected\" ";
            }
            echo ">
\t\t\t\t\t";
            // line 25
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_trnsl", []), "html", null, true);
            echo "
\t\t\t\t</option>
\t\t\t\t<option value=\"need_update\" ";
            // line 27
            if ((($context["trst"] ?? null) == "need_update")) {
                echo " selected=\"selected\" ";
            }
            echo ">
\t\t\t\t\t";
            // line 28
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "need_upd", []), "html", null, true);
            echo "
\t\t\t\t</option>
\t\t\t\t<option value=\"in_progress\" ";
            // line 30
            if ((($context["trst"] ?? null) == "in_progress")) {
                echo " selected=\"selected\" ";
            }
            echo ">
\t\t\t\t\t";
            // line 31
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "in_progress", []), "html", null, true);
            echo "
\t\t\t\t</option>
\t\t\t\t<option value=\"complete\" ";
            // line 33
            if ((($context["trst"] ?? null) == "complete")) {
                echo " selected=\"selected\" ";
            }
            echo ">
\t\t\t\t\t";
            // line 34
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "complete", []), "html", null, true);
            echo "
\t\t\t\t</option>
\t\t\t</select>

\t\t\t<select class=\"wcml_product_status\">
\t\t\t\t<option value=\"all\">";
            // line 39
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "all_stats", []), "html", null, true);
            echo "</option>
\t\t\t\t";
            // line 40
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["all_statuses"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["status"]) {
                // line 41
                echo "\t\t\t\t\t<option value=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $context["status"]);
                echo "\" ";
                if ((($context["st"] ?? null) == $context["status"])) {
                    echo " selected=\"selected\" ";
                }
                echo ">
\t\t\t\t\t\t";
                // line 42
                echo \WPML\Core\twig_escape_filter($this->env, \WPML\Core\twig_capitalize_string_filter($this->env, $context["status"]), "html", null, true);
                echo "
\t\t\t\t\t</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['status'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 45
            echo "\t\t\t</select>

\t\t\t<button type=\"button\" value=\"filter\" class=\"button-secondary wcml_search\">";
            // line 47
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "filter", []), "html", null, true);
            echo "</button>
\t\t\t";
            // line 48
            if (($context["search"] ?? null)) {
                // line 49
                echo "\t\t\t\t<button type=\"button\" value=\"reset\" class=\"button-secondary wcml_reset_search\">
\t\t\t\t\t";
                // line 50
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "reset", []), "html", null, true);
                echo "
\t\t\t\t</button>
\t\t\t";
            }
            // line 53
            echo "\t\t</div>

\t\t<div class=\"alignright\">
\t\t\t<input type=\"search\" class=\"wcml_product_name\" placeholder=\"";
            // line 56
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "search", []));
            echo "\" value=\"";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["search_text"] ?? null), "html", null, true);
            echo "\"/>
\t\t\t<input type=\"hidden\" value=\"";
            // line 57
            echo \WPML\Core\twig_escape_filter($this->env, ($context["products_admin_url"] ?? null), "html", null, true);
            echo "\" class=\"wcml_products_admin_url\"/>
\t\t\t<input type=\"hidden\" value=\"";
            // line 58
            echo \WPML\Core\twig_escape_filter($this->env, ($context["pagination_url"] ?? null), "html", null, true);
            echo "\" class=\"wcml_pagination_url\"/>
\t\t\t<button type=\"button\" value=\"search\" class=\"button-secondary wcml_search_by_title\">";
            // line 59
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "search", []), "html", null, true);
            echo "</button>
\t\t</div>
\t</div>
";
        }
    }

    public function getTemplateName()
    {
        return "filter.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  220 => 59,  216 => 58,  212 => 57,  206 => 56,  201 => 53,  195 => 50,  192 => 49,  190 => 48,  186 => 47,  182 => 45,  173 => 42,  164 => 41,  160 => 40,  156 => 39,  148 => 34,  142 => 33,  137 => 31,  131 => 30,  126 => 28,  120 => 27,  115 => 25,  109 => 24,  105 => 23,  100 => 20,  91 => 17,  82 => 16,  78 => 15,  74 => 14,  69 => 11,  60 => 8,  51 => 7,  47 => 6,  39 => 5,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "filter.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/products-list/filter.twig");
    }
}
