<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Event;

class ComponentEvents
{
    const DISCOVER = 'component.discover';
    const PRE_RENDER = 'component.pre_render';
    const POST_RENDER = 'component.post_render';
}
