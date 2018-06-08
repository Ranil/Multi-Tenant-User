// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @author Owen Tolman <owen@accenagroup.com>
 * @copyright 2018 Owen Tolman
 */

M.tool_multitenantuser = {
    init_select_table: function (Y) {
        Y.use('node', function (Y) {
            radiobuttons = Y.all('#multi_tenant_tool_select_table input');
            radiobuttons.each(function (node) {
                node.on('click', function (e) {

                    current = e.currentTarget.get('name');
                    if (current == 'user') {

                    }
                })
            })
        })
    }
}