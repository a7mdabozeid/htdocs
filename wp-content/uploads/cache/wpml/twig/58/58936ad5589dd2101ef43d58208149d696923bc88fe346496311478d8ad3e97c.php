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

/* store-pages.twig */
class __TwigTemplate_47339a17212ef069761777a9f5dd39b78828e6855f75c46aae57206d841afa0d extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-section wc-store-pages-section\">
    <div class=\"wcml-section-header\">
        <h3>
            ";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "store_pages", []), "html", null, true);
        echo "
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 5
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "pages_trnsl", []));
        echo "\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list\">
            ";
        // line 10
        if ((($context["miss_lang"] ?? null) == "non_exist")) {
            // line 11
            echo "                <li>
                    <i class=\"otgs-ico-warning\"></i>
                    ";
            // line 13
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_created", []), "html", null, true);
            echo "
                </li>
                <li>
                    <a href=\"";
            // line 16
            echo \WPML\Core\twig_escape_filter($this->env, ($context["install_link"] ?? null), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "install", []), "html", null, true);
            echo "</a>
                </li>
            ";
        } elseif (        // line 18
($context["miss_lang"] ?? null)) {
            // line 19
            echo "                <form method=\"post\" action=\"";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["request_uri"] ?? null), "html", null, true);
            echo "\">
                    ";
            // line 20
            echo $this->getAttribute(($context["nonces"] ?? null), "create_pages", []);
            echo "
                    <input type=\"hidden\" name=\"create_missing_pages\" value=\"1\"/>

                    ";
            // line 23
            if ($this->getAttribute(($context["miss_lang"] ?? null), "lang", [], "any", true, true)) {
                // line 24
                echo "                        <li>
                            <i class=\"otgs-ico-warning\"></i>
                            ";
                // line 26
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_exist", []), "html", null, true);
                echo "
                            <ul class=\"wcml-lang-list\">
                                ";
                // line 28
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["miss_lang"] ?? null), "lang", []));
                foreach ($context['_seq'] as $context["_key"] => $context["missed_lang"]) {
                    // line 29
                    echo "                                    <li>
                                        <span class=\"wpml-title-flag\">
                                            <img src=\"";
                    // line 31
                    echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_flag_url')->getCallable(), [$this->getAttribute($context["missed_lang"], "code", [])]), "html", null, true);
                    echo "\" alt=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["missed_lang"], "english_name", []));
                    echo "\">
                                        </span>
                                        ";
                    // line 33
                    echo \WPML\Core\twig_escape_filter($this->env, \WPML\Core\twig_capitalize_string_filter($this->env, $this->getAttribute($context["missed_lang"], "display_name", [])), "html", null, true);
                    echo "
                                    </li>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['missed_lang'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 36
                echo "                            </ul>

                            <button class=\"button-secondary aligncenter\" type=\"submit\" name=\"create_pages\">
                                ";
                // line 39
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "create_transl", []), "html", null, true);
                echo "
                            </button>
                        </li>
                    ";
            }
            // line 43
            echo "
                    ";
            // line 44
            if ($this->getAttribute(($context["miss_lang"] ?? null), "in_progress", [], "any", true, true)) {
                // line 45
                echo "                        <li>
                            <i class=\"otgs-ico-in-progress\"></i>
                            ";
                // line 47
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "translated_wpml", []), "html", null, true);
                echo "
                            <ul class=\"wcml-lang-list\">
                                ";
                // line 49
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["miss_lang"] ?? null), "in_progress", []));
                foreach ($context['_seq'] as $context["_key"] => $context["in_progress_pages"]) {
                    // line 50
                    echo "                                    <li>
                                        <span class=\"wpml-title-flag\">";
                    // line 51
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["in_progress_pages"], "page", []), "html", null, true);
                    echo "</span>
                                        <ul class=\"wcml-lang-list\">
                                            ";
                    // line 53
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["in_progress_pages"], "lang", []));
                    foreach ($context['_seq'] as $context["_key"] => $context["miss_in_progress"]) {
                        // line 54
                        echo "                                                <li>
                                                    <span class=\"wpml-title-flag\">
                                                        <img src=\"";
                        // line 56
                        echo \WPML\Core\twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_flag_url')->getCallable(), [$this->getAttribute($context["miss_in_progress"], "code", [])]), "html", null, true);
                        echo "\" alt=\"";
                        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["miss_in_progress"], "english_name", []));
                        echo "\">
                                                    </span>
                                                    ";
                        // line 58
                        echo \WPML\Core\twig_escape_filter($this->env, \WPML\Core\twig_capitalize_string_filter($this->env, $this->getAttribute($context["miss_in_progress"], "display_name", [])), "html", null, true);
                        echo "
                                                </li>
                                            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['miss_in_progress'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 61
                    echo "                                        </ul>
                                    </li>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['in_progress_pages'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 64
                echo "                            </ul>
                        </li>
                    ";
            }
            // line 67
            echo "                </form>
            ";
        } else {
            // line 69
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 71
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "translated", []), "html", null, true);
            echo "
                </li>
            ";
        }
        // line 74
        echo "        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "store-pages.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  205 => 74,  199 => 71,  195 => 69,  191 => 67,  186 => 64,  178 => 61,  169 => 58,  162 => 56,  158 => 54,  154 => 53,  149 => 51,  146 => 50,  142 => 49,  137 => 47,  133 => 45,  131 => 44,  128 => 43,  121 => 39,  116 => 36,  107 => 33,  100 => 31,  96 => 29,  92 => 28,  87 => 26,  83 => 24,  81 => 23,  75 => 20,  70 => 19,  68 => 18,  61 => 16,  55 => 13,  51 => 11,  49 => 10,  41 => 5,  37 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-section wc-store-pages-section\">
    <div class=\"wcml-section-header\">
        <h3>
            {{ strings.store_pages }}
            <i class=\"otgs-ico-help wcml-tip\" data-tip=\"{{ strings.pages_trnsl|e }}\"></i>
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list\">
            {% if miss_lang == 'non_exist' %}
                <li>
                    <i class=\"otgs-ico-warning\"></i>
                    {{ strings.not_created }}
                </li>
                <li>
                    <a href=\"{{ install_link }}\">{{ strings.install }}</a>
                </li>
            {% elseif miss_lang %}
                <form method=\"post\" action=\"{{ request_uri }}\">
                    {{ nonces.create_pages|raw }}
                    <input type=\"hidden\" name=\"create_missing_pages\" value=\"1\"/>

                    {% if miss_lang.lang is defined %}
                        <li>
                            <i class=\"otgs-ico-warning\"></i>
                            {{ strings.not_exist }}
                            <ul class=\"wcml-lang-list\">
                                {% for missed_lang in miss_lang.lang %}
                                    <li>
                                        <span class=\"wpml-title-flag\">
                                            <img src=\"{{ get_flag_url( missed_lang.code ) }}\" alt=\"{{ missed_lang.english_name|e }}\">
                                        </span>
                                        {{ missed_lang.display_name|capitalize }}
                                    </li>
                                {% endfor %}
                            </ul>

                            <button class=\"button-secondary aligncenter\" type=\"submit\" name=\"create_pages\">
                                {{ strings.create_transl }}
                            </button>
                        </li>
                    {% endif %}

                    {% if miss_lang.in_progress is defined %}
                        <li>
                            <i class=\"otgs-ico-in-progress\"></i>
                            {{ strings.translated_wpml }}
                            <ul class=\"wcml-lang-list\">
                                {% for in_progress_pages in miss_lang.in_progress %}
                                    <li>
                                        <span class=\"wpml-title-flag\">{{ in_progress_pages.page }}</span>
                                        <ul class=\"wcml-lang-list\">
                                            {% for miss_in_progress in in_progress_pages.lang %}
                                                <li>
                                                    <span class=\"wpml-title-flag\">
                                                        <img src=\"{{ get_flag_url( miss_in_progress.code ) }}\" alt=\"{{ miss_in_progress.english_name|e }}\">
                                                    </span>
                                                    {{ miss_in_progress.display_name|capitalize }}
                                                </li>
                                            {% endfor %}
                                        </ul>
                                    </li>
                                {% endfor %}
                            </ul>
                        </li>
                    {% endif %}
                </form>
            {% else %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.translated }}
                </li>
            {% endif %}
        </ul>
    </div>
</div>", "store-pages.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/store-pages.twig");
    }
}
