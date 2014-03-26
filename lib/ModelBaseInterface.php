<?php

/**
 * Base interface to all RedBean models
 *
 * @author eventurers
 */

interface ModelBaseInterface {

    /**
     * Validate the model
     * @return bool
     */
    public function validate();

    /**
     * Creating the new user
     */
    public function create();

    /**
     * validate the entity
     */
    public function validateCreate();

}