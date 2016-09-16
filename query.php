<?php

class DB_Query {
    private $db;

    private $query;

    public function __construct() {
        // Assign DB object
        $this->db = JFactory::getDbo();

        // Assign Query
        $this->query = $this->db->getQuery(true);
    }

    // -------------------------------------------------------------------------
    // Settings

    // Default Query Settings
    protected $queryDefaults = [
        'table' => null,
        'author' => null,
        'author_userName' => null,
        'state' => 'published',
        'sort' => 'ASC',
        'internal_group' => 'id'
    ];

    // Query Settings
    protected $querySettings = [];

    // -------------------------------------------------------------------------
    // The Query and Results

    protected $results = [];

    // -------------------------------------------------------------------------
    // Query Helpers

    // Table Query
    private function tableQuery() {
        // Build FROM query
        $this->query->from($this->db->quoteName('#__' . $this->querySettings['table']));
    }

    // Author Query
    private function authorQuery() {
        // Build FROM query
        $this->query->where('created_by' . ' = ' . $this->querySettings['author']);
    }

    // Author Username Query
    private function authorUsernameQuery() {
        $userId = JUserHelper::getUserId($this->querySettings['author_userName']);

        $this->query->where('created_by' . ' = ' . $userId);
    }

    // State Query
    private function stateQuery() {
        // Build WHERE query
        $this->query->where($this->db->quoteName('state') . " = " . 1);
    }

    // Sort Query
    private function sortQuery() {
        // Build ORDER query
        $this->query->order('ordering ASC');
    }

    private function internalGroupQuery() {
        // build GROUP query
        $this->query->group($this->db->quoteName($this->querySettings['internal_group']));
    }

    // -------------------------------------------------------------------------
    // Build Query

    private function buildQuery() {

        // $query
        //     ->select('*')
        //     ->from($db->quoteName('#__' . $this->querySettings['table']))
        //     ->where($db->quoteName('state') . " = " . 1)
        //     ->order('ordering ASC')
        //     ->group($db->quoteName('id'));

        // Build SELECT query
        $this->query->select('*');

        // Sort through all the query settings and compile the query
        foreach ($this->querySettings as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'table':
                        $this->tableQuery();
                        break;

                    case 'author':
                        $this->authorQuery();
                        break;

                    case 'author_userName':
                        $this->authorUsernameQuery();
                        break;

                    case 'state':
                        $this->stateQuery();
                        break;

                    case 'sort':
                        $this->sortQuery();
                        break;

                    case 'internal_group':
                        $this->internalGroupQuery();
                        break;
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Execute Query

    private function executeQuery() {
        $this->db->setQuery($this->query);

        $results = $this->db->loadObjectList();

        return $results;
    }

    // -------------------------------------------------------------------------
    // Database Query

    public function databaseQuery($args = []) {
        // Merge the query args with the settings arary
        $this->querySettings = array_merge($this->queryDefaults, $args);

        // - - - - - - - - - - - - - - - - - - - -
        // Build the query

        $this->buildQuery();

        // - - - - - - - - - - - - - - - - - - - -
        // Execute and return results

        $this->results = $this->executeQuery();

        // Sort the results
        // $this->sortResults();

        return $this->results;
    }
}
