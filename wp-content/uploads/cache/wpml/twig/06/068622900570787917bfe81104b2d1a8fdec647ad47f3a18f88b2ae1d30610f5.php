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

/* sync-taxonomy.twig */
class __TwigTemplate_cac7e0546a2e47d1ae6cb43d0c104eaf1d7eff74468c79ccaaf92e31810cd1bb extends \WPML\Core\Twig\Template
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
        echo "<div class=\"icl_tt_main_bottom\" style=\"display: none;\">
    <br/>
    ";
        // line 3
        if (($context["attribute_taxonomies"] ?? null)) {
            // line 4
            echo "        <form id=\"wcml_tt_sync_variations\" method=\"post\">
            <input type=\"hidden\" name=\"action\" value=\"wcml_sync_product_variations\" />
            <input type=\"hidden\" id=\"warn_title\" value=\"";
            // line 6
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "untranstaled_warn", []), "html", null, true);
            echo "\" />
            <input type=\"hidden\" name=\"taxonomy\" value=\"";
            // line 7
            echo \WPML\Core\twig_escape_filter($this->env, ($context["taxonomy"] ?? null), "html", null, true);
            echo "\" />
            ";
            // line 8
            echo $this->getAttribute(($context["nonces"] ?? null), "sync_product_variations", []);
            echo "
            <input type=\"hidden\" name=\"last_post_id\" value=\"\" />
            <input type=\"hidden\" name=\"languages_processed\" value=\"0\" />
            <div id=\"wcml_tt_sync_assignment\" style=\"";
            // line 11
            echo \WPML\Core\twig_escape_filter($this->env, ($context["display"] ?? null));
            echo "\" >
                <p>
                    <input class=\"button-secondary\" type=\"submit\" value=\"";
            // line 13
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "sync_update", []));
            echo "\" />
                    <img src=\"";
            // line 14
            echo \WPML\Core\twig_escape_filter($this->env, ($context["loader_url"] ?? null));
            echo "\" alt=\"loading\" height=\"16\" width=\"16\" class=\"wcml_tt_spinner\" />
                </p>
                <span class=\"errors icl_error_text\"></span>
            </div>
            <div class=\"wcml_tt_sycn_preview\"></div>
        </form>

        <div id=\"wcml_tt_sync_desc\" style=\"";
            // line 21
            echo \WPML\Core\twig_escape_filter($this->env, ($context["display_attr"] ?? null));
            echo "\">
            <p>";
            // line 22
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "auto_generate", []), "html", null, true);
            echo "</p>
            ";
            // line 23
            if ( !twig_test_empty(($context["vars_to_create"] ?? null))) {
                // line 24
                echo "                <p>
                    ";
                // line 25
                echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "vars_to_create", []), ($context["vars_to_create"] ?? null)), "html", null, true);
                echo "
                </p>
            ";
            }
            // line 28
            echo "        </div>
    ";
        } else {
            // line 30
            echo "        <form id=\"wcml_tt_sync_assignment\" style=\"";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["display_tax"] ?? null));
            echo "\" data-sync=\"";
            if (($context["display_tax"] ?? null)) {
                echo "0";
            } else {
                echo "1";
            }
            echo "\">
            <input type=\"hidden\" name=\"taxonomy\" value=\"";
            // line 31
            echo \WPML\Core\twig_escape_filter($this->env, ($context["taxonomy"] ?? null), "html", null, true);
            echo "\"/>
            ";
            // line 32
            echo $this->getAttribute(($context["nonces"] ?? null), "sync_taxonomies", []);
            echo "
            <p>
                <input class=\"button-secondary\" type=\"submit\" value=\"";
            // line 34
            echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "sync_in_cont", []), ($context["tax_name"] ?? null)));
            echo "\"/>
                <img src=\"";
            // line 35
            echo \WPML\Core\twig_escape_filter($this->env, ($context["loader_url"] ?? null));
            echo "\" alt=\"loading\" height=\"16\" width=\"16\" class=\"wcml_tt_spinner\"/>
            </p>
            <span class=\"errors icl_error_text\"></span>
        </form>
        <div id=\"wcml_tt_sync_preview\"></div>


        <p id=\"wcml_tt_sync_desc\" style=\"";
            // line 42
            echo \WPML\Core\twig_escape_filter($this->env, ($context["display_tax"] ?? null));
            echo "\">
            ";
            // line 43
            echo sprintf($this->getAttribute(($context["strings"] ?? null), "auto_apply", []), ($context["tax_singular_name"] ?? null));
            echo "
        </p>
    ";
        }
        // line 46
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "sync-taxonomy.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  142 => 46,  136 => 43,  132 => 42,  122 => 35,  118 => 34,  113 => 32,  109 => 31,  98 => 30,  94 => 28,  88 => 25,  85 => 24,  83 => 23,  79 => 22,  75 => 21,  65 => 14,  61 => 13,  56 => 11,  50 => 8,  46 => 7,  42 => 6,  38 => 4,  36 => 3,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"icl_tt_main_bottom\" style=\"display: none;\">
    <br/>
    {% if attribute_taxonomies %}
        <form id=\"wcml_tt_sync_variations\" method=\"post\">
            <input type=\"hidden\" name=\"action\" value=\"wcml_sync_product_variations\" />
            <input type=\"hidden\" id=\"warn_title\" value=\"{{ strings.untranstaled_warn }}\" />
            <input type=\"hidden\" name=\"taxonomy\" value=\"{{ taxonomy }}\" />
            {{ nonces.sync_product_variations|raw }}
            <input type=\"hidden\" name=\"last_post_id\" value=\"\" />
            <input type=\"hidden\" name=\"languages_processed\" value=\"0\" />
            <div id=\"wcml_tt_sync_assignment\" style=\"{{ display|e }}\" >
                <p>
                    <input class=\"button-secondary\" type=\"submit\" value=\"{{ strings.sync_update|e }}\" />
                    <img src=\"{{ loader_url|e }}\" alt=\"loading\" height=\"16\" width=\"16\" class=\"wcml_tt_spinner\" />
                </p>
                <span class=\"errors icl_error_text\"></span>
            </div>
            <div class=\"wcml_tt_sycn_preview\"></div>
        </form>

        <div id=\"wcml_tt_sync_desc\" style=\"{{ display_attr|e }}\">
            <p>{{ strings.auto_generate }}</p>
            {% if vars_to_create is not empty %}
                <p>
                    {{ strings.vars_to_create|format( vars_to_create ) }}
                </p>
            {% endif %}
        </div>
    {% else %}
        <form id=\"wcml_tt_sync_assignment\" style=\"{{ display_tax|e }}\" data-sync=\"{% if display_tax %}0{% else %}1{% endif %}\">
            <input type=\"hidden\" name=\"taxonomy\" value=\"{{ taxonomy }}\"/>
            {{ nonces.sync_taxonomies|raw }}
            <p>
                <input class=\"button-secondary\" type=\"submit\" value=\"{{ strings.sync_in_cont|format( tax_name )|e }}\"/>
                <img src=\"{{ loader_url|e }}\" alt=\"loading\" height=\"16\" width=\"16\" class=\"wcml_tt_spinner\"/>
            </p>
            <span class=\"errors icl_error_text\"></span>
        </form>
        <div id=\"wcml_tt_sync_preview\"></div>


        <p id=\"wcml_tt_sync_desc\" style=\"{{ display_tax|e }}\">
            {{ strings.auto_apply|format( tax_singular_name )|raw }}
        </p>
    {% endif %}
</div>", "sync-taxonomy.twig", "/home/alshia6/public_html/wp-content/plugins/woocommerce-multilingual/templates/sync-taxonomy.twig");
    }
}
