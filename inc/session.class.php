<?php
/**
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Behaviors plugin for GLPI.

 Behaviors is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Behaviors is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Behaviors. If not, see <http://www.gnu.org/licenses/>.

 @package   behaviors
 @author    Remi Collet, Nelly Mahu-Lasson, Riccardo Bicelli
 @copyright Copyright (c) 2010-2022 Behaviors plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/behaviors
 @link      http://www.glpi-project.org/
 @since     version 0.83.4

 --------------------------------------------------------------------------
*/

use Group;

class PluginBehaviorsSession extends PluginBehaviorsCommon {   

   
   static function LoadChildGroups() {
   
      $config = PluginBehaviorsConfig::getInstance();

      if (($config->getField('show_child_groups_items') > 0)) { 
         
         foreach ( $_SESSION["glpigroups"] as $glpi_group ) {       
         
            self::LoadGroupsRecursive($glpi_group);
         
         }
      
      }
   }

   /**
    * Iterate through direct assigned groups and adds child groups
    * to session variable, in order to let the search and permissions
    * work with parent groups
    * @param mixed $group_id
    * @return void
    */
   static function LoadGroupsRecursive($group_id){
    
      /** @var \DBmysql $DB */
      global $DB;
  
      $iterator = $DB->request([
          'SELECT'    => Group::getTable() . '.id',
          'FROM'      => Group::getTable(),            
          'WHERE'     => [
              Group::getTable() . '.groups_id' => $group_id
          ] + getEntitiesRestrictCriteria(
              Group::getTable(),
              'entities_id',
              $_SESSION['glpiactiveentities'],
              true
          )
      ]);
  
      foreach ($iterator as $data) {

          $_SESSION["glpigroups"][] = $data["id"];          
          self::LoadGroupsRecursive($data["id"]);
      
      }
  
  }
}
