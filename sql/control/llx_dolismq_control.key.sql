-- Copyright (C) 2022 EVARISK <dev@evarisk.com>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.

ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_rowid (rowid);
ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_ref (ref);
ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_status (status);
ALTER TABLE llx_dolismq_control ADD CONSTRAINT llx_dolismq_control_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_fk_sheet (fk_sheet);
ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_fk_user_controller (fk_user_controller);
ALTER TABLE llx_dolismq_control ADD INDEX idx_dolismq_control_fk_project (fk_project);
