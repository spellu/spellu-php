<?php

namespace Spellu;

use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

class SourcePrinter extends PrettyPrinter
{
    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes)
    {
        $this->canUseSemicolonNamespaces = false;
    }
}