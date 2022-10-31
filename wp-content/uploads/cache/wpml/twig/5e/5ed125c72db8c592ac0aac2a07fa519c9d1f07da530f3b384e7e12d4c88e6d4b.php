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

/* products.twig */
class __TwigTemplate_928a759288887f8597be0f06d49fe892481094165e54d8fa3a8a36b772b85109 extends \WPML\Core\Twig\Template
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
        echo \WPML\Core\twig_escape_filter($this->env, ($context["request_uri"] ?? null));
        echo "\">

\t";
        // line 3
        echo ($context["auto_translate_container"] ?? null);
        echo "

\t";
        // line 5
        $this->loadTemplate("filter.twig", "products.twig", 5)->display(twig_array_merge($context, ($context["filter"] ?? null)));
        // line 6
        echo "
\t<div class=\"wcml-table-responsive\">
\t\t<table class=\"widefat fixed wpml-list-table wp-list-table striped\" cellspacing=\"0\">
\t\t\t<thead>
\t\t\t\t<tr>
\t\t\t\t\t<th scope=\"col\" class=\"column-thumb\">
\t\t\t\t\t\t\t<span class=\"wc-image wcml-tip\"
\t\t\t\t\t\t\t\t  data-tip=\"";
        // line 13
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "image", []));
        echo "\">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "image", []), "html", null, true);
        echo "</span>
\t\t\t\t\t</th>
\t\t\t\t\t<th scope=\"col\" class=\"wpml-col-title ";
        // line 15
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["filter_urls"] ?? null), "product_sorted", []), "html", null, true);
        echo "\">
\t\t\t\t\t\t<a href=\"";
        // line 16
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["filter_urls"] ?? null), "product", []));
        echo "\">
\t\t\t\t\t\t\t<span>";
        // line 17
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "product", []), "html", null, true);
        echo "</span>
\t\t\t\t\t\t\t<span class=\"sorting-indicator\"></span>
\t\t\t\t\t\t</a>
\t\t\t\t\t</th>
\t\t\t\t\t<th scope=\"col\" class=\"wpml-col-languages\">
\t\t\t\t\t\t";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages_flags"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 23
            echo "\t\t\t\t\t\t\t<span title=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "name", []));
            echo "\">
\t\t\t\t\t\t\t\t<img src=\"";
            // line 24
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "flag_url", []), "html", null, true);
            echo "\"  alt=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "name", []));
            echo "\"/>
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        echo "\t\t\t\t\t</th>
\t\t\t\t\t<th scope=\"col\"
\t\t\t\t\t\tclass=\"column-categories\">";
        // line 29
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "categories", []), "html", null, true);
        echo "</th>
\t\t\t\t\t";
        // line 30
        if ($this->getAttribute(($context["strings"] ?? null), "type", [])) {
            // line 31
            echo "\t\t\t\t\t\t<th scope=\"col\" class=\"column-product_type\">
\t\t\t\t\t\t\t\t<span class=\"wc-type wcml-tip\"
\t\t\t\t\t\t\t\t\t  data-tip=\"";
            // line 33
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "type", []));
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "type", []), "html", null, true);
            echo "</span>
\t\t\t\t\t\t</th>
\t\t\t\t\t";
        }
        // line 36
        echo "\t\t\t\t\t<th scope=\"col\" id=\"date\" class=\"column-date ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["filter_urls"] ?? null), "date_sorted", []), "html", null, true);
        echo "\">
\t\t\t\t\t\t<a href=\"";
        // line 37
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["filter_urls"] ?? null), "date", []));
        echo "\">
\t\t\t\t\t\t\t<span>";
        // line 38
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "date", []), "html", null, true);
        echo "</span>
\t\t\t\t\t\t\t<span class=\"sorting-indicator\"></span>
\t\t\t\t\t\t</a>
\t\t\t\t\t</th>
\t\t\t\t</tr>
\t\t\t</thead>

\t\t\t<tbody>
\t\t\t\t";
        // line 46
        if (twig_test_empty($this->getAttribute(($context["data"] ?? null), "products", []))) {
            // line 47
            echo "\t\t\t\t\t<tr>
\t\t\t\t\t\t<td colspan=\"6\" class=\"text-center\">
\t\t\t\t\t\t\t<strong>";
            // line 49
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "no_products", []), "html", null, true);
            echo "</strong>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t";
        } else {
            // line 53
            echo "\t\t\t\t\t";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "products", []));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 54
                echo "\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t<td class=\"thumb column-thumb\">
\t\t\t\t\t\t\t\t<a href=\"";
                // line 56
                echo $this->getAttribute($context["product"], "edit_post_link", []);
                echo "\">
\t\t\t\t\t\t\t\t\t<img width=\"150\" height=\"150\" src=\"";
                // line 57
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "post_thumbnail", []), "html", null, true);
                echo "\" class=\"wp-post-image original-image\" data-image-id=\"thumbnail-";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "ID", []), "html", null, true);
                echo "\" >
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t</td>

\t\t\t\t\t\t\t";
                // line 62
                echo "\t\t\t\t\t\t\t";
                if ($this->getAttribute($context["product"], "has_image", [])) {
                    // line 63
                    echo "\t\t\t\t\t\t\t\t<img id=\"thumbnail-";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "ID", []), "html", null, true);
                    echo "\" src=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "post_thumbnail", []), "html", null, true);
                    echo "\" class=\"mouse-hovered\">
\t\t\t\t\t\t\t";
                }
                // line 65
                echo "
\t\t\t\t\t\t\t<td class=\"wpml-col-title  wpml-col-title-flag\">
\t\t\t\t\t\t\t\t";
                // line 67
                if (($this->getAttribute($context["product"], "post_parent", []) != 0)) {
                    echo " &#8212; ";
                }
                // line 68
                echo "\t\t\t\t\t\t\t\t<strong>
\t\t\t\t\t\t\t\t\t";
                // line 69
                if ( !$this->getAttribute(($context["data"] ?? null), "slang", [])) {
                    // line 70
                    echo "\t\t\t\t\t\t\t\t\t\t<span class=\"wpml-title-flag\">
\t\t\t\t\t\t\t\t\t\t\t<img src=\"";
                    // line 71
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "orig_flag_url", []), "html", null, true);
                    echo "\"/>
\t\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 74
                echo "
\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 75
                echo $this->getAttribute($context["product"], "edit_post_link", []);
                echo "\" title=\"";
                echo \WPML\Core\twig_escape_filter($this->env, strip_tags($this->getAttribute($context["product"], "post_title", [])), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t\t\t\t";
                // line 76
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "post_title", []), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t";
                // line 79
                if (($this->getAttribute($context["product"], "post_status", []) == "draft")) {
                    // line 80
                    echo "\t\t\t\t\t\t\t\t\t\t- <span class=\"post-state\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "draft", []), "html", null, true);
                    echo "</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 82
                echo "
\t\t\t\t\t\t\t\t\t";
                // line 83
                if (($this->getAttribute($context["product"], "post_status", []) == "private")) {
                    // line 84
                    echo "\t\t\t\t\t\t\t\t\t\t- <span class=\"post-state\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "private", []), "html", null, true);
                    echo "</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 86
                echo "
\t\t\t\t\t\t\t\t\t";
                // line 87
                if (($this->getAttribute($context["product"], "post_status", []) == "pending")) {
                    // line 88
                    echo "\t\t\t\t\t\t\t\t\t\t- <span class=\"post-state\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "pending", []), "html", null, true);
                    echo "</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 90
                echo "
\t\t\t\t\t\t\t\t\t";
                // line 91
                if (($this->getAttribute($context["product"], "post_status", []) == "future")) {
                    // line 92
                    echo "\t\t\t\t\t\t\t\t\t\t- <span class=\"post-state\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "future", []), "html", null, true);
                    echo "</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 94
                echo "
\t\t\t\t\t\t\t\t\t";
                // line 95
                if (($this->getAttribute(($context["data"] ?? null), "search", []) && ($this->getAttribute($context["product"], "post_parent", []) != 0))) {
                    // line 96
                    echo "\t\t\t\t\t\t\t\t\t\t| <span class=\"prod_parent_text\">
\t\t\t\t\t\t\t\t\t\t\t";
                    // line 97
                    echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "parent", []), $this->getAttribute($context["product"], "parent_title", [])), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t\t";
                }
                // line 100
                echo "\t\t\t\t\t\t\t\t</strong>

\t\t\t\t\t\t\t\t<div class=\"row-actions\">
\t\t\t\t\t\t\t\t\t<span class=\"edit\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 104
                echo $this->getAttribute($context["product"], "edit_post_link", []);
                echo "\"
\t\t\t\t\t\t\t\t\t\t   title=\"";
                // line 105
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "edit_item", []));
                echo "\">";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "edit", []), "html", null, true);
                echo "</a>
\t\t\t\t\t\t\t\t\t</span> | <span class=\"view\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 107
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "view_link", []));
                echo "\"
\t\t\t\t\t\t\t\t\t\t   title=\"";
                // line 108
                echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "view_link", []), $this->getAttribute($context["product"], "post_title", [])));
                echo "\" target=\"_blank\">";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "view", []), "html", null, true);
                echo "</a>
\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t</td>

\t\t\t\t\t\t\t<td class=\"wpml-col-languages\">
\t\t\t\t\t\t\t\t";
                // line 115
                echo $this->getAttribute($context["product"], "translation_statuses", []);
                echo "
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td class=\"column-categories\">
\t\t\t\t\t\t\t\t";
                // line 118
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["product"], "categories_list", []));
                foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                    // line 119
                    echo "\t\t\t\t\t\t\t\t\t<a href=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["category"], "href", []));
                    echo "\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["category"], "name", []), "html", null, true);
                    echo "</a>
\t\t\t\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 121
                echo "\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t";
                // line 122
                if ($this->getAttribute(($context["strings"] ?? null), "type", [])) {
                    // line 123
                    echo "\t\t\t\t\t\t\t\t<td class=\"column-product_type\">
\t\t\t\t\t\t\t\t\t<span class=\"product-type wcml-tip ";
                    // line 124
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "icon_class", []));
                    echo "\"
\t\t\t\t\t\t\t\t\t\t  data-tip=\"";
                    // line 125
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "icon_class", []));
                    echo "\"></span>
\t\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t";
                }
                // line 128
                echo "
\t\t\t\t\t\t\t<td class=\"column-date\">
\t\t\t\t\t\t\t\t";
                // line 130
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["product"], "formated_date", []), "html", null, true);
                echo "

\t\t\t\t\t\t\t\t";
                // line 132
                if (($this->getAttribute($context["product"], "post_status", []) == "publish")) {
                    // line 133
                    echo "\t\t\t\t\t\t\t\t\t<br>";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "published", []), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t";
                } else {
                    // line 135
                    echo "\t\t\t\t\t\t\t\t\t<br>";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "modified", []), "html", null, true);
                    echo "
\t\t\t\t\t\t\t\t";
                }
                // line 137
                echo "\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 140
            echo "\t\t\t\t";
        }
        // line 141
        echo "\t\t\t</tbody>
\t\t</table>
\t</div>

\t<input type=\"hidden\" id=\"upd_product_nonce\" value=\"";
        // line 145
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["nonces"] ?? null), "upd_product", []));
        echo "\" />
\t<input type=\"hidden\" id=\"get_product_data_nonce\" value=\"";
        // line 146
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["nonces"] ?? null), "get_product_data", []));
        echo "\" />

\t";
        // line 148
        $this->loadTemplate("pagination.twig", "products.twig", 148)->display(twig_array_merge($context, ($context["pagination"] ?? null)));
        // line 149
        echo "</form>
";
    }

    public function getTemplateName()
    {
        return "products.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  400 => 149,  398 => 148,  393 => 146,  389 => 145,  383 => 141,  380 => 140,  372 => 137,  366 => 135,  360 => 133,  358 => 132,  353 => 130,  349 => 128,  343 => 125,  339 => 124,  336 => 123,  334 => 122,  331 => 121,  320 => 119,  316 => 118,  310 => 115,  298 => 108,  294 => 107,  287 => 105,  283 => 104,  277 => 100,  271 => 97,  268 => 96,  266 => 95,  263 => 94,  257 => 92,  255 => 91,  252 => 90,  246 => 88,  244 => 87,  241 => 86,  235 => 84,  233 => 83,  230 => 82,  224 => 80,  222 => 79,  216 => 76,  210 => 75,  207 => 74,  201 => 71,  198 => 70,  196 => 69,  193 => 68,  189 => 67,  185 => 65,  177 => 63,  174 => 62,  165 => 57,  161 => 56,  157 => 54,  152 => 53,  145 => 49,  141 => 47,  139 => 46,  128 => 38,  124 => 37,  119 => 36,  111 => 33,  107 => 31,  105 => 30,  101 => 29,  97 => 27,  86 => 24,  81 => 23,  77 => 22,  69 => 17,  65 => 16,  61 => 15,  54 => 13,  45 => 6,  43 => 5,  38 => 3,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "products.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/products-list/products.twig");
    }
}
