<?php

return [
	/**
	 * The default Notion API version to use.
	 */
	'version' => 'v1',

    /**
     * The default Notion API version to use.
     */
    'version_header' => [
        'v1' => '2021-05-13'
    ],

	/**
	 * Your Notion API token.
	 */
    'notion-api-token' => env('NOTION_API_TOKEN', ''),

    /**
     * HTTP client configuration.
     */
    'retry' => 3,

    'timeout' => 10,

    'sleep' => 3000,
];