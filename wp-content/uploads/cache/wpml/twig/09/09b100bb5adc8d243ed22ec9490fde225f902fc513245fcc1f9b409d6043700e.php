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

/* taxonomies.twig */
class __TwigTemplate_a309e390bbee6bd6e7aeb960168ab3e5931a63cb46c9bfe755a4e1b9b9c0a55b extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-section wc-missing-translation-section\">
    <div class=\"wcml-section-header\">
        <h3>
            ";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "tax_missing", []), "html", null, true);
        echo "
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 5
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "run_site", []));
        echo "\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content js-tax-translation\">
        <ul class=\"wcml-status-list wcml-tax-translation-list\">
            ";
        // line 10
        $context["no_tax_to_update"] = true;
        // line 11
        echo "            ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["taxonomies"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["taxonomy"]) {
            // line 12
            echo "\t\t\t    ";
            $context["no_tax_to_update"] = false;
            // line 13
            echo "                <li class=\"js-tax-translation-";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "tax", []), "html", null, true);
            echo "\">
                    ";
            // line 14
            if ($this->getAttribute($context["taxonomy"], "untranslated", [])) {
                // line 15
                echo "                        ";
                if ($this->getAttribute($context["taxonomy"], "fully_trans", [])) {
                    // line 16
                    echo "                            <i class=\"otgs-ico-ok\"></i>
                            ";
                    // line 17
                    echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "not_req_trnsl", []), $this->getAttribute($context["taxonomy"], "name", [])), "html", null, true);
                    echo "
                        ";
                } else {
                    // line 19
                    echo "                            <i class=\"otgs-ico-warning\"></i>
                            ";
                    // line 20
                    if (($this->getAttribute($context["taxonomy"], "untranslated", []) == 1)) {
                        // line 21
                        echo "                                ";
                        echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "miss_trnsl_one", []), $this->getAttribute($context["taxonomy"], "untranslated", []), $this->getAttribute($context["taxonomy"], "name_singular", [])), "html", null, true);
                        echo "
                            ";
                    } else {
                        // line 23
                        echo "                                ";
                        echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "miss_trnsl_more", []), $this->getAttribute($context["taxonomy"], "untranslated", []), $this->getAttribute($context["taxonomy"], "name", [])), "html", null, true);
                        echo "
                            ";
                    }
                    // line 25
                    echo "                            <a class=\"button button-secondary button-small\" href=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "url", []), "html", null, true);
                    echo "\">
                                ";
                    // line 26
                    echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "trnsl", []), $this->getAttribute($context["taxonomy"], "name", [])), "html", null, true);
                    echo "
                            </a>
                        ";
                }
                // line 29
                echo "                    ";
            } else {
                // line 30
                echo "                        <i class=\"otgs-ico-ok\"></i>
                        ";
                // line 31
                echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "all_trnsl", []), $this->getAttribute($context["taxonomy"], "name", [])), "html", null, true);
                echo "
                    ";
            }
            // line 33
            echo "                </li>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['taxonomy'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "            ";
        if (($context["no_tax_to_update"] ?? null)) {
            // line 36
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 38
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_to_trnsl", []), "html", null, true);
            echo "
                </li>
            ";
        }
        // line 41
        echo "        </ul>
        <span>";
        // line 42
        echo $this->getAttribute(($context["strings"] ?? null), "conf_warning", []);
        echo "</span>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "taxonomies.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 42,  136 => 41,  130 => 38,  126 => 36,  123 => 35,  116 => 33,  111 => 31,  108 => 30,  105 => 29,  99 => 26,  94 => 25,  88 => 23,  82 => 21,  80 => 20,  77 => 19,  72 => 17,  69 => 16,  66 => 15,  64 => 14,  59 => 13,  56 => 12,  51 => 11,  49 => 10,  41 => 5,  37 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-section wc-missing-translation-section\">
    <div class=\"wcml-section-header\">
        <h3>
            {{ strings.tax_missing }}
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ strings.run_site|e }}\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content js-tax-translation\">
        <ul class=\"wcml-status-list wcml-tax-translation-list\">
            {% set no_tax_to_update = true %}
            {% for taxonomy in taxonomies %}
\t\t\t    {% set no_tax_to_update = false %}
                <li class=\"js-tax-translation-{{ taxonomy.tax }}\">
                    {% if taxonomy.untranslated %}
                        {% if taxonomy.fully_trans %}
                            <i class=\"otgs-ico-ok\"></i>
                            {{ strings.not_req_trnsl|format( taxonomy.name ) }}
                        {% else %}
                            <i class=\"otgs-ico-warning\"></i>
                            {% if( taxonomy.untranslated == 1) %}
                                {{ strings.miss_trnsl_one|format( taxonomy.untranslated, taxonomy.name_singular ) }}
                            {% else %}
                                {{ strings.miss_trnsl_more|format( taxonomy.untranslated, taxonomy.name ) }}
                            {% endif %}
                            <a class=\"button button-secondary button-small\" href=\"{{ taxonomy.url }}\">
                                {{ strings.trnsl|format( taxonomy.name ) }}
                            </a>
                        {% endif %}
                    {% else %}
                        <i class=\"otgs-ico-ok\"></i>
                        {{ strings.all_trnsl|format( taxonomy.name ) }}
                    {% endif %}
                </li>
            {% endfor %}
            {% if no_tax_to_update %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.not_to_trnsl }}
                </li>
            {% endif %}
        </ul>
        <span>{{ strings.conf_warning|raw }}</span>
    </div>
</div>", "taxonomies.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/taxonomies.twig");
    }
}
