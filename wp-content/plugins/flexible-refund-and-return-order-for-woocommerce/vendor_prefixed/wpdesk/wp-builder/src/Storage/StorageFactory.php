<?php

namespace FRFreeVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \FRFreeVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
