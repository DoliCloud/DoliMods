
-- To move user elements onto another user
update llx_societe set fk_user_creat = newid where fk_user_creat = oldid;
update llx_societe set fk_user_modif = newid where fk_user_modif = oldid;
update llx_socpeople set fk_user_creat = newid where fk_user_creat = oldid;
update llx_socpeople set fk_user_modif = newid where fk_user_modif = oldid;
