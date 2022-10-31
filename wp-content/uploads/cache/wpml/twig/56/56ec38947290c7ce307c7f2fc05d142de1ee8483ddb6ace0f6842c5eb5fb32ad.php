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

/* translation-statuses.twig */
class __TwigTemplate_87589fe5598ff9d9f6d908c02e8dba7d2dc3f476c36c42226656494ea9296fa1 extends \WPML\Core\Twig\Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["active_languages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 2
            echo "    ";
            if (($this->getAttribute($context["language"], "code", []) == ($context["source_language"] ?? null))) {
                // line 3
                echo "        <span title=\"";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "english_name", []));
                echo ": ";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "orig_lang", []));
                echo "\">
                    <i class=\"otgs-ico-original\"></i>
        </span>
    ";
            } else {
                // line 7
                echo "        ";
                echo call_user_func_array($this->env->getFunction('wcml_base_edit_dialog')->getCallable(), [($context["base"] ?? null), $this->getAttribute($context["language"], "code", [])]);
                echo "

        <a class=\"js-wcml-dialog-trigger ";
                // line 9
                if ( !($context["value"] ?? null)) {
                    echo "dis_base";
                }
                echo "\" id=\"wcml-edit-base-slug-";
                echo \WPML\Core\twig_escape_filter($this->env, ($context["base"] ?? null));
                echo "-";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []));
                echo "-link\"
           data-dialog=\"wcml-edit-base-slug-";
                // line 10
                echo \WPML\Core\twig_escape_filter($this->env, ($context["base"] ?? null));
                echo "-";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []));
                echo "\"
           data-content=\"wcml-edit-base-slug-";
                // line 11
                echo \WPML\Core\twig_escape_filter($this->env, ($context["base"] ?? null));
                echo "-";
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []));
                echo "\"  data-width=\"700\" data-height=\"150\"

            ";
                // line 13
                if (($this->getAttribute($context["language"], "status", []) == "upd")) {
                    // line 14
                    echo "                title=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "english_name", []));
                    echo ": ";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "update", []));
                    echo "\">
                    <i class=\"otgs-ico-refresh\"></i>
            ";
                } elseif (($this->getAttribute(                // line 16
$context["language"], "status", []) == "add")) {
                    // line 17
                    echo "                title=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "english_name", []));
                    echo ": ";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "add", []));
                    echo "\">
                    <i class=\"otgs-ico-add\"></i>
            ";
                } elseif (($this->getAttribute(                // line 19
$context["language"], "status", []) == "edit")) {
                    // line 20
                    echo "                title=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "english_name", []));
                    echo ": ";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "edit", []));
                    echo ">\">
                <i class=\"otgs-ico-edit\"></i>
            ";
                }
                // line 23
                echo "        </a>
    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "translation-statuses.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 23,  100 => 20,  98 => 19,  90 => 17,  88 => 16,  80 => 14,  78 => 13,  71 => 11,  65 => 10,  55 => 9,  49 => 7,  39 => 3,  36 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% for language in active_languages %}
    {% if language.code == source_language %}
        <span title=\"{{ language.english_name|e }}: {{ strings.orig_lang|e }}\">
                    <i class=\"otgs-ico-original\"></i>
        </span>
    {% else %}
        {{ wcml_base_edit_dialog( base, language.code )|raw }}

        <a class=\"js-wcml-dialog-trigger {% if not value %}dis_base{% endif %}\" id=\"wcml-edit-base-slug-{{ base|e }}-{{ language.code|e }}-link\"
           data-dialog=\"wcml-edit-base-slug-{{ base|e }}-{{ language.code|e }}\"
           data-content=\"wcml-edit-base-slug-{{ base|e }}-{{ language.code|e }}\"  data-width=\"700\" data-height=\"150\"

            {% if language.status == 'upd' %}
                title=\"{{ language.english_name|e }}: {{ strings.update|e }}\">
                    <i class=\"otgs-ico-refresh\"></i>
            {% elseif language.status == 'add' %}
                title=\"{{ language.english_name|e }}: {{ strings.add|e }}\">
                    <i class=\"otgs-ico-add\"></i>
            {% elseif language.status == 'edit' %}
                title=\"{{ language.english_name|e }}: {{ strings.edit|e }}>\">
                <i class=\"otgs-ico-edit\"></i>
            {% endif %}
        </a>
    {% endif %}
{% endfor %}", "translation-statuses.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/store-urls/translation-statuses.twig");
    }
}
