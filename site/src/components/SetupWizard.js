
import React, {Component} from 'react';
import './SetupWizard.scss';

class SetupWizard extends Component {
    constructor(props) {
        super(props);
        this.state = {flavor: 'drupal'}
        this.chooseFlavor = this.chooseFlavor.bind(this);
    }
    chooseFlavor(e) {
        this.setState({flavor: e.target.getAttribute('data-flavor')});
    }
    render() {
        const {flavor} = this.state;
        const component = getComponent(flavor);

        return (
            <div className="SetupWizard">
                <ul className="WizardTabs">
                    <li><a onClick={this.chooseFlavor} data-flavor="html" aria-selected={flavor === 'html'}>HTML</a></li>
                    <li><a onClick={this.chooseFlavor} data-flavor="twig" aria-selected={flavor === 'twig'}>Twig</a></li>
                    <li><a onClick={this.chooseFlavor} data-flavor="drupal" aria-selected={flavor === 'drupal'}>Drupal</a></li>
                </ul>
                {component}
            </div>
        )
    }
}

export default SetupWizard;

function getComponent(flavor) {
    switch(flavor) {
        case 'html':
            return <HtmlConfig />
        case 'twig':
            return <TwigConfig />
        case 'drupal':
            return <DrupalConfig />
    }
}

function Config({form, require, children}) {
    return (
        <div className="WizardConfig">
            <form>{form}</form>
            <dl>
                <dt>Step 1: Install Dependencies</dt>
                <dd><pre><code>{`composer require ${require}`}</code></pre></dd>
                <dt>Step 2: Create a .mannequin.php file</dt>
                <dd>{children}</dd>
            </dl>
        </div>
    )
}

class HtmlConfig extends Component {
    constructor(props) {
        super(props);
        this.state = {templates: '/'}
        this.changeTemplates = this.changeTemplates.bind(this);
    }
    getForm() {
        return (
            <div>
                <label>My HTML files are in:
                    <input type="text" value={this.state.templates} onChange={this.changeTemplates} />
                </label>
            </div>
        )
    }
    changeTemplates(e) {
        this.setState({templates: e.target.value})
    }
    render() {
        const form = this.getForm();
        return (
            <Config form={form} require="lastcall/mannequin-html">
                <pre><code>{`<?php
use LastCall\\Mannequin\\Core\\MannequinConfig;
use LastCall\\Mannequin\\Html\\HtmlExtension;
use Symfony\\Component\\Finder\\Finder;

$htmlFinder = Finder::create()
    ->in(__DIR__.'${this.state.templates}')
    ->files()
    ->name('*.html');

$htmlExtension = new HtmlExtension([
    'files' => $htmlFinder,
    'root' => __DIR__
]);

return MannequinConfig::create()
->addExtension($htmlExtension);`}</code></pre>
            </Config>
        )
    }
}

class TwigConfig extends Component {
    constructor(props) {
        super(props);
        this.state = {templates: '/Resources/views'}
        this.changeTemplates = this.changeTemplates.bind(this);
    }
    getForm() {
        return (
            <div>
                <label>My Twig templates are in:
                    <input type="text" value={this.state.templates} onChange={this.changeTemplates} />
                </label>
            </div>
        )
    }
    changeTemplates(e) {
        this.setState({templates: e.target.value})
    }
    render() {
        const form = this.getForm();
        return (
            <Config form={form} require="lastcall/mannequin-twig">
                <pre><code>{`<?php
use LastCall\\Mannequin\\Core\\MannequinConfig;
use LastCall\\Mannequin\\Twig\\TwigExtension;
use Symfony\\Component\\Finder\\Finder;

$twigFinder = Finder::create()
    ->in(__DIR__.'${this.state.templates}')
    ->files()
    ->name('*.twig');

$twigExtension = new TwigExtension([
    'finder' => $htmlFinder,
    'twig_root' => __DIR__.'${this.state.templates}',
]);

return MannequinConfig::create()
  ->addExtension($twigExtension);`}</code></pre>
            </Config>
        )
    }
}

class DrupalConfig extends Component {
    constructor(props) {
        super(props);
        this.state = {templates: '/themes/custom/*/templates'}
        this.changeTemplates = this.changeTemplates.bind(this);
    }
    getForm() {
        return (
            <div>
                <label>My Twig templates are in:
                    <input type="text" value={this.state.templates} onChange={this.changeTemplates} />
                </label>
            </div>
        )
    }
    changeTemplates(e) {
        this.setState({templates: e.target.value})
    }
    render() {
        const form = this.getForm();
        return (
            <Config form={form} require="lastcall/mannequin-drupal">
                <pre><code>{`<?php
use LastCall\\Mannequin\\Core\\MannequinConfig;
use LastCall\\Mannequin\\Drupal\\DrupalExtension;
use Symfony\\Component\\Finder\\Finder;

$drupalFinder = Finder::create()
    ->in(__DIR__.'${this.state.templates}')
    ->files()
    ->name('*.twig');

$drupalExtension = new DrupalExtension([
    'finder' => $drupalFinder,
    'drupal_root' => __DIR__,
]);

return MannequinConfig::create()
  ->addExtension($drupalExtension);`}</code></pre>
            </Config>
        )
    }
}
