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
class __TwigTemplate_8f434ad6a4f3f8a4a4cc2da8536691ece928e37b37f654991e816e60a008f3b7 extends \WPML\Core\Twig\Template
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
        return new Source("{% if display %}
\t<div class=\"tablenav top clearfix wcml-product-translation-filtering\">
\t\t<div class=\"alignleft\">
\t\t\t<select class=\"wcml_translation_status_lang\">
\t\t\t\t<option value=\"all\" {% if not slang %} selected=\"selected\" {% endif %}>{{ strings.all_lang }}</option>
\t\t\t\t{% for language in active_languages %}
\t\t\t\t\t<option\tvalue=\"{{ language.code|e }}\" {% if slang == language.code  %} selected=\"selected\" {% endif %} >
\t\t\t\t\t\t{{ language.display_name }}
\t\t\t\t\t</option>
\t\t\t\t{% endfor %}
\t\t\t</select>

\t\t\t<select class=\"wcml_product_category\">
\t\t\t\t<option value=\"0\">{{ strings.all_cats }}</option>
\t\t\t\t{% for category in categories %}
\t\t\t\t\t<option value=\"{{ category.term_taxonomy_id|e }}\" {% if category_from_filter == category.term_taxonomy_id %} selected=\"selected\" {% endif %}>
\t\t\t\t\t\t{{ category.name }}
\t\t\t\t\t</option>
\t\t\t\t{% endfor %}
\t\t\t</select>

\t\t\t<select class=\"wcml_translation_status\">
\t\t\t\t<option value=\"all\">{{ strings.all_trnsl_stats }}</option>
\t\t\t\t<option value=\"not\" {% if trst == 'not' %} selected=\"selected\" {% endif %}>
\t\t\t\t\t{{ strings.not_trnsl }}
\t\t\t\t</option>
\t\t\t\t<option value=\"need_update\" {% if trst == 'need_update' %} selected=\"selected\" {% endif %}>
\t\t\t\t\t{{ strings.need_upd }}
\t\t\t\t</option>
\t\t\t\t<option value=\"in_progress\" {% if trst == 'in_progress' %} selected=\"selected\" {% endif %}>
\t\t\t\t\t{{ strings.in_progress }}
\t\t\t\t</option>
\t\t\t\t<option value=\"complete\" {% if trst == 'complete' %} selected=\"selected\" {% endif %}>
\t\t\t\t\t{{ strings.complete }}
\t\t\t\t</option>
\t\t\t</select>

\t\t\t<select class=\"wcml_product_status\">
\t\t\t\t<option value=\"all\">{{ strings.all_stats }}</option>
\t\t\t\t{% for status in all_statuses %}
\t\t\t\t\t<option value=\"{{ status|e }}\" {% if st == status %} selected=\"selected\" {% endif %}>
\t\t\t\t\t\t{{ status|capitalize }}
\t\t\t\t\t</option>
\t\t\t\t{% endfor %}
\t\t\t</select>

\t\t\t<button type=\"button\" value=\"filter\" class=\"button-secondary wcml_search\">{{ strings.filter }}</button>
\t\t\t{% if search %}
\t\t\t\t<button type=\"button\" value=\"reset\" class=\"button-secondary wcml_reset_search\">
\t\t\t\t\t{{ strings.reset }}
\t\t\t\t</button>
\t\t\t{% endif %}
\t\t</div>

\t\t<div class=\"alignright\">
\t\t\t<input type=\"search\" class=\"wcml_product_name\" placeholder=\"{{ strings.search|e }}\" value=\"{{ search_text }}\"/>
\t\t\t<input type=\"hidden\" value=\"{{ products_admin_url }}\" class=\"wcml_products_admin_url\"/>
\t\t\t<input type=\"hidden\" value=\"{{ pagination_url }}\" class=\"wcml_pagination_url\"/>
\t\t\t<button type=\"button\" value=\"search\" class=\"button-secondary wcml_search_by_title\">{{ strings.search }}</button>
\t\t</div>
\t</div>
{% endif %}", "filter.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/products-list/filter.twig");
    }
}
