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

/* nav-menus-full.twig */
class __TwigTemplate_31cf32b911543d3fc205fa590e3ce499ba3d0131aed15125e2ce4dc517fcb307 extends \WPML\Core\Twig\Template
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
        echo "<nav class=\"wcml-tabs wpml-tabs\">
    <a class=\"nav-tab ";
        // line 2
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "products", []), "active", []), "html", null, true);
        echo "\" href=\"";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "products", []), "url", []), "html", null, true);
        echo "\">";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "products", []), "title", []), "html", null, true);
        echo "</a>
    ";
        // line 3
        if (($context["can_operate_options"] ?? null)) {
            // line 4
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["menu"] ?? null), "taxonomies", []));
            foreach ($context['_seq'] as $context["key"] => $context["taxonomy"]) {
                // line 5
                echo "            ";
                if ($this->getAttribute($context["taxonomy"], "is_translatable", [])) {
                    // line 6
                    echo "                <a class=\"js-tax-tab-";
                    echo \WPML\Core\twig_escape_filter($this->env, $context["key"], "html", null, true);
                    echo " nav-tab ";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "active", []), "html", null, true);
                    echo "\" href=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "url", []), "html", null, true);
                    echo "\" title=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "title", []), "html", null, true);
                    echo "\">
                    ";
                    // line 7
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "name", []), "html", null, true);
                    echo "
                    ";
                    // line 8
                    if (($this->getAttribute($context["taxonomy"], "translated", []) == false)) {
                        echo "<i class=\"otgs-ico-warning\"></i>";
                    }
                    // line 9
                    echo "                </a>
            ";
                }
                // line 11
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['taxonomy'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "        ";
            if ($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "custom_taxonomies", []), "show", [])) {
                // line 13
                echo "            <a class=\"nav-tab tax-custom-taxonomies ";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "custom_taxonomies", []), "active", []), "html", null, true);
                echo "\" href=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "custom_taxonomies", []), "url", []), "html", null, true);
                echo "\">
                ";
                // line 14
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "custom_taxonomies", []), "name", []), "html", null, true);
                echo "
                ";
                // line 15
                if (($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "custom_taxonomies", []), "translated", []) == false)) {
                    echo "<i class=\"otgs-ico-warning\"></i>";
                }
                // line 16
                echo "            </a>
        ";
            }
            // line 18
            echo "        <a class=\"nav-tab tax-product-attributes ";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "attributes", []), "active", []), "html", null, true);
            echo "\" href=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "attributes", []), "url", []), "html", null, true);
            echo "\">
            ";
            // line 19
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "attributes", []), "name", []), "html", null, true);
            echo "
            ";
            // line 20
            if (($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "attributes", []), "translated", []) == false)) {
                echo "<i class=\"otgs-ico-warning\"></i>";
            }
            // line 21
            echo "        </a>
        ";
            // line 22
            if ($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "is_translatable", [])) {
                // line 23
                echo "            <a class=\"js-tax-tab-product_shipping_class nav-tab ";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "active", []), "html", null, true);
                echo "\" href=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "url", []), "html", null, true);
                echo "\"
               title=\"";
                // line 24
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "title", []), "html", null, true);
                echo "\">";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "name", []), "html", null, true);
                echo "
                ";
                // line 25
                if (($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "shipping_classes", []), "translated", []) == false)) {
                    echo "<i class=\"otgs-ico-warning\"></i>";
                }
                // line 26
                echo "            </a>
        ";
            }
            // line 28
            echo "    ";
        }
        // line 29
        echo "
    ";
        // line 30
        if (($context["can_manage_options"] ?? null)) {
            // line 31
            echo "        <a class=\"nav-tab ";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "settings", []), "active", []), "html", null, true);
            echo "\" href=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "settings", []), "url", []), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "settings", []), "name", []), "html", null, true);
            echo "</a>
    ";
        }
        // line 33
        echo "    ";
        if (($context["can_operate_options"] ?? null)) {
            // line 34
            echo "        <a class=\"nav-tab ";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "slugs", []), "active", []), "html", null, true);
            echo "\" href=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "slugs", []), "url", []), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "slugs", []), "name", []), "html", null, true);
            echo "</a>
    ";
        }
        // line 36
        echo "    ";
        if (($context["can_manage_options"] ?? null)) {
            // line 37
            echo "        <a class=\"nav-tab ";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "status", []), "active", []), "html", null, true);
            echo "\" href=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "status", []), "url", []), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "status", []), "name", []), "html", null, true);
            echo "</a>
        ";
            // line 38
            if ($this->getAttribute($this->getAttribute(($context["menu"] ?? null), "troubleshooting", []), "active", [])) {
                // line 39
                echo "            <a class=\"nav-tab troubleshooting ";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "troubleshooting", []), "active", []), "html", null, true);
                echo "\" href=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "troubleshooting", []), "url", []), "html", null, true);
                echo "\">";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "troubleshooting", []), "name", []), "html", null, true);
                echo "</a>
        ";
            }
            // line 41
            echo "    ";
        }
        // line 42
        echo "    ";
        if (($context["can_operate_options"] ?? null)) {
            // line 43
            echo "        <a class=\"nav-tab ";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "multi_currency", []), "active", []), "html", null, true);
            echo "\" href=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "multi_currency", []), "url", []), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["menu"] ?? null), "multi_currency", []), "name", []), "html", null, true);
            echo "</a>
    ";
        }
        // line 45
        echo "</nav>";
    }

    public function getTemplateName()
    {
        return "nav-menus-full.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  216 => 45,  206 => 43,  203 => 42,  200 => 41,  190 => 39,  188 => 38,  179 => 37,  176 => 36,  166 => 34,  163 => 33,  153 => 31,  151 => 30,  148 => 29,  145 => 28,  141 => 26,  137 => 25,  131 => 24,  124 => 23,  122 => 22,  119 => 21,  115 => 20,  111 => 19,  104 => 18,  100 => 16,  96 => 15,  92 => 14,  85 => 13,  82 => 12,  76 => 11,  72 => 9,  68 => 8,  64 => 7,  53 => 6,  50 => 5,  45 => 4,  43 => 3,  35 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<nav class=\"wcml-tabs wpml-tabs\">
    <a class=\"nav-tab {{ menu.products.active }}\" href=\"{{ menu.products.url }}\">{{ menu.products.title }}</a>
    {%  if can_operate_options %}
        {% for key, taxonomy in menu.taxonomies %}
            {% if taxonomy.is_translatable %}
                <a class=\"js-tax-tab-{{ key }} nav-tab {{ taxonomy.active }}\" href=\"{{ taxonomy.url }}\" title=\"{{ taxonomy.title }}\">
                    {{ taxonomy.name }}
                    {% if taxonomy.translated == false %}<i class=\"otgs-ico-warning\"></i>{% endif %}
                </a>
            {% endif %}
        {% endfor %}
        {%  if menu.custom_taxonomies.show  %}
            <a class=\"nav-tab tax-custom-taxonomies {{ menu.custom_taxonomies.active }}\" href=\"{{ menu.custom_taxonomies.url }}\">
                {{ menu.custom_taxonomies.name }}
                {% if menu.custom_taxonomies.translated == false %}<i class=\"otgs-ico-warning\"></i>{% endif %}
            </a>
        {% endif %}
        <a class=\"nav-tab tax-product-attributes {{ menu.attributes.active }}\" href=\"{{ menu.attributes.url }}\">
            {{ menu.attributes.name }}
            {% if menu.attributes.translated == false %}<i class=\"otgs-ico-warning\"></i>{% endif %}
        </a>
        {% if menu.shipping_classes.is_translatable %}
            <a class=\"js-tax-tab-product_shipping_class nav-tab {{ menu.shipping_classes.active }}\" href=\"{{ menu.shipping_classes.url }}\"
               title=\"{{ menu.shipping_classes.title }}\">{{ menu.shipping_classes.name }}
                {% if menu.shipping_classes.translated == false %}<i class=\"otgs-ico-warning\"></i>{% endif %}
            </a>
        {% endif %}
    {% endif %}

    {% if can_manage_options %}
        <a class=\"nav-tab {{ menu.settings.active }}\" href=\"{{ menu.settings.url }}\">{{ menu.settings.name }}</a>
    {% endif %}
    {%  if can_operate_options %}
        <a class=\"nav-tab {{ menu.slugs.active }}\" href=\"{{ menu.slugs.url }}\">{{ menu.slugs.name }}</a>
    {% endif %}
    {% if can_manage_options %}
        <a class=\"nav-tab {{ menu.status.active }}\" href=\"{{ menu.status.url }}\">{{ menu.status.name }}</a>
        {% if menu.troubleshooting.active %}
            <a class=\"nav-tab troubleshooting {{ menu.troubleshooting.active }}\" href=\"{{ menu.troubleshooting.url }}\">{{ menu.troubleshooting.name }}</a>
        {% endif %}
    {% endif %}
    {%  if can_operate_options %}
        <a class=\"nav-tab {{ menu.multi_currency.active }}\" href=\"{{ menu.multi_currency.url }}\">{{ menu.multi_currency.name }}</a>
    {% endif %}
</nav>", "nav-menus-full.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/nav-menus-full.twig");
    }
}
