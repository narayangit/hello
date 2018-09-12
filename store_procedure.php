Store Procedure

store procedure name "atten"
----------------------------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `atten`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `atten`(IN startdate DATE, IN enddate DATE)
BEGIN
DECLARE crap INT;
DECLARE done INT DEFAULT 0;
DECLARE username1 VARCHAR(250);
DECLARE username2 VARCHAR(250);
DECLARE uname1 VARCHAR(250);
DECLARE totaltime1 INT;
DECLARE tdate1 DATE;
DECLARE stat VARCHAR(20);
DECLARE chk INT;
DECLARE dname VARCHAR(100);
DECLARE ecn1 VARCHAR(40);
DECLARE cur_2 CURSOR FOR SELECT uname,totaltime,tdate FROM temp3 WHERE tdate BETWEEN startdate AND enddate AND NOT uname IS NULL;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
OPEN cur_2;
loop2:LOOP
	
    FETCH cur_2 INTO uname1,totaltime1,tdate1;
    IF done = 1 THEN
			LEAVE loop2;	
	END IF;
    
    SET ecn1 = (SELECT ecn FROM empuid WHERE uid = uname1);
    
    SET username1 = (SELECT uname FROM mirror.user WHERE ecn = REPLACE(ecn1, ' ',''));
    
 
    SET dname = DAYNAME(tdate1);
    IF totaltime1 > 240 THEN
		SET stat = 'P';
        
     ELSEIF totaltime1 BETWEEN 60 AND 240 THEN
		SET stat = 'HLF';
	 ELSE
		SET stat ='A';
    END IF;
    SELECT ecn1,username1;
    SET chk = (SELECT COUNT(NAME) FROM present WHERE NAME= username1 AND username = uname1 AND tdate = tdate1);
    IF chk > 1 THEN
		UPDATE present SET STATUS =stat, totaltime = totaltime1 WHERE NAME= username1 AND username = uname1 AND tdate = tdate1;
        IF done = 1 THEN
			LEAVE loop2;	
	END IF;
    ELSE
		INSERT INTO present (present.name,username,tdate,STATUS,totaltime,CHANGED,sup) VALUES(username1,uname1,tdate1,stat,totaltime1,'','');
        IF done = 1 THEN
			LEAVE loop2;	
	END IF;
    END IF;
    
END LOOP loop2;
CLOSE cur_2;
END$$

DELIMITER ;
-------------------------------------------------------------------------------------------

"auto_delete"
-------------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `auto_delete`$$

CREATE DEFINER=`root`@`%` PROCEDURE `auto_delete`(IN dt DATE)
BEGIN
	
	DECLARE t_day DATE;
	DECLARE uid VARCHAR(200);
	DECLARE cname VARCHAR(200);
	DECLARE act VARCHAR(100);
	DECLARE my_time TIME;
	DECLARE stat VARCHAR(100);
	DECLARE done BOOLEAN DEFAULT FALSE;
	DECLARE cur_1 CURSOR FOR SELECT compname,username,mytime,tday,active,STATUS FROM att WHERE tday=dt;
	   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	   -- SET dt=DATE_ADD(CURRENT_DATE,INTERVAL -3 DAY);
	   OPEN cur_1;
		my_loop1:LOOP
			IF done THEN
			LEAVE my_loop1;
			CLOSE cur_1;	
			END IF;
			FETCH cur_1 INTO cname,uid,my_time,t_day,act,stat;
			INSERT INTO temp_att(compname,username,tday,mytime,STATUS,active) VALUES (cname,uid,t_day,my_time,stat,act);
		END LOOP my_loop1;
		DELETE FROM att WHERE tday=dt;
	   CLOSE cur_1;
    END$$

DELIMITER ;
-------------------------------------------------------------------------------------------

"auto_insert"
-------------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `auto_insert`$$

CREATE DEFINER=`root`@`%` PROCEDURE `auto_insert`(IN dt DATE,IN pro VARCHAR(200))
BEGIN
	
		DECLARE nxt_dt DATE;
		DECLARE shift_start TIME;
		DECLARE shiftstart TIME;
		DECLARE shift_start_1 TIME;
		DECLARE shift_end TIME;
		DECLARE shiftend TIME;
		DECLARE shift_end_1 TIME;
		DECLARE first_time TIME;
		DECLARE first_time_1 TIME;
		DECLARE tot_active_time INT DEFAULT 0;
		DECLARE tot_break_time INT DEFAULT 0;
		DECLARE tot_meeting_time INT DEFAULT 0;
		DECLARE tot_event_time INT DEFAULT 0;
		DECLARE tot_medical_time INT DEFAULT 0;
		DECLARE tot_down_time INT DEFAULT 0;
		DECLARE tot_nocall_time INT DEFAULT 0;
		DECLARE tot_train_time INT DEFAULT 0;
		DECLARE tot_callibration_time INT DEFAULT 0;
		DECLARE tot_active_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time INT DEFAULT 0;
		DECLARE tot_idle_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time_2 INT DEFAULT 0;
		DECLARE c INT DEFAULT 0;
		DECLARE c_1 INT;
		DECLARE mytime_2 TIME;
		DECLARE mytime_3 TIME;
		DECLARE next_time TIME;
		DECLARE next_time_1 TIME;
		DECLARE last_time TIME;
		DECLARE last_time_1 TIME;
		DECLARE time_diff TIME;
		DECLARE time_diff_1 TIME;
		DECLARE uid_1 VARCHAR(200);
		DECLARE ecn_1 VARCHAR(200);
		DECLARE name_1 VARCHAR(200);
		DECLARE stat_2 VARCHAR(200);
		DECLARE chkstat VARCHAR(200);
		DECLARE chkstat1 VARCHAR(200);
		DECLARE stat_3 VARCHAR(200);
		DECLARE active_2 TEXT;
		DECLARE active_3 TEXT;
		DECLARE brk_1 INT DEFAULT 0;
		DECLARE meeting_1 INT DEFAULT 0;
		DECLARE calib_1 INT DEFAULT 0;
		DECLARE event_1 INT DEFAULT 0;
		DECLARE med_1 INT DEFAULT 0;
		DECLARE down_1 INT DEFAULT 0;
		DECLARE nocall_1 INT DEFAULT 0;
		DECLARE train_1 INT DEFAULT 0;
		DECLARE brk_2 INT DEFAULT 0;
		DECLARE meeting_2 INT DEFAULT 0;
		DECLARE calib_2 INT DEFAULT 0;
		DECLARE event_2 INT DEFAULT 0;
		DECLARE med_2 INT DEFAULT 0;
		DECLARE down_2 INT DEFAULT 0;
		DECLARE nocall_2 INT DEFAULT 0;
		DECLARE train_2 INT DEFAULT 0;
		DECLARE idl INT;
		DECLARE act INT;
		DECLARE act1 INT;
		DECLARE idl_1 INT;
		DECLARE totl INT;
		DECLARE u INT;
		DECLARE done BOOLEAN DEFAULT FALSE;
	   DECLARE cur_1 CURSOR FOR SELECT ecn,uid,NAME FROM empuid WHERE PROCESS=pro;
	   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	   -- SET dt=DATE_ADD(CURRENT_DATE,INTERVAL -3 DAY);
	   OPEN cur_1;
		my_loop1:LOOP
			FETCH cur_1 INTO ecn_1,uid_1,name_1;
			IF done THEN
			LEAVE my_loop1;
			END IF;
			SET tot_break_time =0;
			SET tot_meeting_time =0;
			SET tot_event_time =0;
			SET tot_medical_time =0;
			SET tot_down_time =0;
			SET tot_callibration_time =0;
			SET tot_active_time_1=0;
			SET tot_idle_time_1=0;
			SET brk_2=0;
			SET meeting_2=0;
			SET calib_2=0;
			SET event_2=0;
			SET down_2=0;
			SET med_2=0;	
			SET nocall_2=0;
			SET train_2=0;
			SET tot_active_time=0;
			SET tot_idle_time=0;
			SET brk_1=0;
			SET meeting_1=0;
			SET calib_1=0;
			SET event_1=0;
			SET down_1=0;
			SET med_1=0;
			SET nocall_1=0;
			SET train_1=0;
			-- select uid_1,name_1;
			
			SET shift_start=(SELECT mytime FROM att WHERE username=uid_1 AND tday=dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime >'06:00:00' ORDER BY mytime ASC LIMIT 1);
			SET shiftstart=shift_start;
			IF shift_start IS NOT NULL THEN
 				SET shift_end=(SELECT mytime FROM att WHERE username=uid_1 AND tday=dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='23:59:59' ORDER BY mytime DESC LIMIT 1);
 				SET shiftend=shift_end;
 				block2 : BEGIN
 				DECLARE done2 BOOLEAN DEFAULT FALSE;
 				DECLARE cur_2 CURSOR FOR SELECT mytime,STATUS,active FROM att WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start;
 				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
 				SET first_time = (SELECT MIN(mytime) FROM att WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%'));
 				
 				
					SET tot_active_time=0;
					SET tot_idle_time=0;
					SET brk_1=0;
					SET meeting_1=0;
					SET calib_1=0;
					SET event_1=0;
					SET down_1=0;
					SET med_1=0;
					SET nocall_1=0;
					SET train_1=0;
					SET c=0;
					OPEN cur_2;
 					my_loop2:LOOP
 					FETCH cur_2 INTO mytime_2,stat_2,active_2;
 					IF done2 THEN
 					LEAVE my_loop2;
 					END IF;
 					SET next_time=mytime_2;
					SET time_diff=TIMEDIFF(next_time,first_time);
					-- SELECT shift_start,shift_end,active_2,uid_1,first_time,next_time,time_diff,stat_2;
					IF stat_2='Idle' THEN
						SET idl=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_idle_time=tot_idle_time+idl;
					ELSEIF stat_2='Active' THEN
						-- SET c=c+1;
						SET act=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_active_time=tot_active_time+act;
					ELSEIF stat_2='START BREAK' OR stat_2='START MEETING' OR stat_2='START CALLIBRATION' OR stat_2='START EVENT' OR stat_2='START MEDICAL ISSUE' OR stat_2='START NO CALLS' OR stat_2='START DOWN TIME' OR stat_2='START TRAINING' THEN
						
						SET chkstat=(SELECT STATUS FROM att WHERE tday=dt AND username=uid_1 AND mytime<mytime_2 ORDER BY mytime DESC LIMIT 1);
						IF chkstat='Active' OR chkstat='END BREAK' OR chkstat='END MEETING' OR chkstat='END CALLIBRATION' OR chkstat='END EVENT' OR 'END MEDICAL ISSUE' OR chkstat='END NO CALLS' OR chkstat='END DOWN TIME' OR chkstat='END TRAINING' THEN
						SET act=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_active_time=tot_active_time+act;
						ELSEIF chkstat='Idle' THEN
						SET idl=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_idle_time=tot_idle_time+idl;
						END IF;
						
					ELSEIF stat_2='END BREAK' THEN
						SET brk_1=brk_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEETING' THEN
						SET meeting_1= meeting_1 + (SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END CALLIBRATION' THEN
						SET calib_1= calib_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END EVENT' THEN
						SET event_1= event_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEDICAL ISSUE' THEN
						SET med_1= med_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END DOWN TIME' THEN
						SET down_1= down_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END NO CALLS' THEN
						SET nocall_1= nocall_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END TRAINING' THEN
						SET train_1= train_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					END IF;
					SET first_time=next_time;
					
					END LOOP my_loop2;
					-- SELECT tot_idle_time; 
					/*SET tot_idle_time=tot_idle_time-(brk_1+meeting_1+calib_1+event_1+med_1+down_1);
					if (tot_idle_time<0) then
					set tot_idle_time=0;
					end if;*/
					
					-- SELECT uid_1,shift_start,shift_end,tot_active_time,tot_idle_time,brk_1,meeting_1,calib_1,event_1,med_1,down_1;
				CLOSE cur_2;
				END block2;
				
				-- SET shift_end=DATE_FORMAT(shift_end, '%H:%i:%s');
				IF shift_end>'19:30:00' AND shift_end<'23:59:59' THEN
				
					SET nxt_dt=DATE_ADD(dt,INTERVAL 1 DAY);
					SET shift_start_1=(SELECT mytime AS shift_start FROM att WHERE username=uid_1 AND tday=nxt_dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime>='00:00:00' ORDER BY mytime ASC LIMIT 1);
					IF shift_start_1 IS NOT NULL THEN
					SET shift_end_1=(SELECT mytime AS shift_end FROM att WHERE username=uid_1 AND tday=nxt_dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='06:00:00' ORDER BY mytime DESC LIMIT 1);
					SET shiftend=shift_end_1;
					block3 : BEGIN
					DECLARE done3 BOOLEAN DEFAULT FALSE;
					DECLARE cur_3 CURSOR FOR SELECT mytime,STATUS,active FROM att WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1  AND mytime >=shift_start_1;
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done3 = TRUE;
					SET first_time_1 = (SELECT MIN(mytime) FROM att WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1 AND mytime >=shift_start_1);
					SET tot_active_time_1=0;
					SET tot_idle_time_1=0;
					SET brk_2=0;
					SET meeting_2=0;
					SET calib_2=0;
					SET event_2=0;
					SET down_2=0;
					SET med_2=0;
					SET nocall_2=0;
					SET train_2=0;
					SET c_1=0;
					OPEN cur_3;
						my_loop3:LOOP
						FETCH cur_3 INTO mytime_3,stat_3,active_3;
						IF done3 THEN
						LEAVE my_loop3;
						END IF;
						SET next_time_1=mytime_3;
						SET time_diff_1=TIMEDIFF(next_time_1,first_time_1);
						-- SELECT stat_3,mytime_3,active_3;
						IF stat_3='Idle' THEN
							SET idl_1=ROUND(TIME_TO_SEC(time_diff_1)/60,0);
							SET tot_idle_time_1=tot_idle_time_1+idl_1;
						ELSEIF stat_3='Active' THEN
						
						-- SET c=c+1;
							SET act=(SELECT ROUND(TIME_TO_SEC(time_diff_1)/60,0));
							SET tot_active_time_1=tot_active_time_1+act;
						ELSEIF stat_3='START BREAK' OR stat_3='START MEETING' OR stat_3='START CALLIBRATION' OR stat_3='START EVENT' OR stat_3='START MEDICAL ISSUE' OR stat_3='START NO CALLS' OR stat_3='START DOWN TIME' OR stat_3='START TRAINING' THEN
						
							SET chkstat1=(SELECT STATUS FROM att WHERE tday=nxt_dt AND username=uid_1 AND mytime<mytime_3 ORDER BY mytime DESC LIMIT 1);
							IF chkstat1='Active' OR chkstat1='END BREAK' OR chkstat1='END MEETING' OR chkstat1='END CALLIBRATION' OR chkstat1='END EVENT' OR chkstat1='END MEDICAL ISSUE' OR chkstat1='END NO CALLS' OR chkstat1='END DOWN TIME' OR chkstat1='END TRAINING' THEN
							SET act=(SELECT ROUND(TIME_TO_SEC(time_diff_1)/60,0));
							SET tot_active_time_1=tot_active_time_1+act;
							ELSEIF chkstat1='Idle' THEN
							SET idl_1=ROUND(TIME_TO_SEC(time_diff_1)/60,0);
							SET tot_idle_time_1=tot_idle_time_1+idl_1;
							END IF;
						ELSEIF stat_3='END BREAK' THEN
							SET brk_2=brk_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEETING' THEN
							SET meeting_2=meeting_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END CALLIBRATION' THEN
							SET calib_2=calib_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END EVENT' THEN
						-- SELECT mytime_3,stat_3,active_3;
							SET event_2=event_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEDICAL ISSUE' THEN
							SET med_2=med_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END DOWN TIME'  THEN
							SET down_2=down_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END NO CALLS'  THEN
							SET nocall_2=nocall_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END TRAINING'  THEN
							SET train_2=train_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						END IF;
						SET first_time_1=next_time_1;
						
						END LOOP my_loop3;
						
						/*SET tot_idle_time_1=tot_idle_time_1-(brk_2+meeting_2+calib_2+event_2+med_2+down_2);
						IF (tot_idle_time_1<0) THEN
						SET tot_idle_time_1=0;
						END IF;*/
						-- SET tot_active_time_1=c_1*5;
						-- SELECT uid_1,shift_start_1,shift_end_1,tot_active_time_1,tot_idle_time_1,brk_2,meeting_2,calib_2,event_2,med_2,nocall_2;	
						
					CLOSE cur_3;
					END block3;
					
					
					END IF;
					
				END IF;
					
					SET tot_break_time=tot_break_time+brk_1+brk_2;
					SET tot_meeting_time=meeting_1+meeting_2;
					SET tot_callibration_time=calib_1+calib_2;
					SET tot_event_time=event_1+event_2;
					SET tot_medical_time=med_1+med_2;
					SET tot_down_time=down_1+down_2;
					SET tot_nocall_time=nocall_1+nocall_2;
					SET tot_train_time=train_1+train_2;
					SET tot_active_time=tot_active_time+tot_active_time_1;
					SET tot_idle_time=tot_idle_time+tot_idle_time_1;
				SET totl=tot_active_time+tot_idle_time+tot_break_time+tot_meeting_time+tot_callibration_time+tot_event_time+tot_medical_time+tot_down_time+tot_nocall_time+tot_train_time;
				
				SET u=(SELECT ROUND(((tot_active_time+tot_meeting_time+tot_callibration_time+tot_event_time+tot_medical_time+tot_nocall_time+tot_train_time)/totl)*100));	
				
				-- SELECT uid_1,u,totl,tot_active_time,tot_idle_time,tot_break_time,tot_meeting_time,tot_callibration_time,tot_event_time,tot_medical_time,tot_down_time,tot_nocall_time;
				IF u <> 0  THEN
				INSERT INTO util(ecn,uname,PROCESS,util_perc,totl,totl_active_time,totl_idle_time,break_time,meeting_time,callibration_time,event_time,medical_time,down_time,nocall_time,train_time,shift_start,shift_end,DATE) VALUES (ecn_1,name_1,pro,u,totl,tot_active_time,tot_idle_time,tot_break_time,tot_meeting_time,tot_callibration_time,tot_event_time,tot_medical_time,tot_down_time,tot_nocall_time,tot_train_time,shiftstart,shiftend,dt);
				END IF;
 			END IF;
 			
		END LOOP my_loop1;
	   CLOSE cur_1;
	   END$$

DELIMITER ;

--------------------------------------------------------------------------------------------------

"auto_select"
-------------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `auto_select`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `auto_select`(IN dt DATE,IN pro VARCHAR(200))
BEGIN
		
		DECLARE nxt_dt DATE;
		DECLARE shift_start TIME;
		DECLARE shift_start_1 TIME;
		DECLARE shift_end TIME;
		DECLARE shift_end_1 TIME;
		DECLARE first_time TIME;
		DECLARE first_time_1 TIME;
		DECLARE tot_active_time INT DEFAULT 0;
		DECLARE tot_break_time INT DEFAULT 0;
		DECLARE tot_meeting_time INT DEFAULT 0;
		DECLARE tot_event_time INT DEFAULT 0;
		DECLARE tot_medical_time INT DEFAULT 0;
		DECLARE tot_down_time INT DEFAULT 0;
		DECLARE tot_nocall_time INT DEFAULT 0;
		DECLARE tot_callibration_time INT DEFAULT 0;
		DECLARE tot_active_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time INT DEFAULT 0;
		DECLARE tot_idle_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time_2 INT;
		DECLARE c INT DEFAULT 0;
		DECLARE c_1 INT;
		DECLARE mytime_2 TIME;
		DECLARE mytime_3 TIME;
		DECLARE next_time TIME;
		DECLARE next_time_1 TIME;
		DECLARE last_time TIME;
		DECLARE last_time_1 TIME;
		DECLARE time_diff TIME;
		DECLARE time_diff_1 TIME;
		DECLARE uid_1 VARCHAR(200);
		DECLARE name_1 VARCHAR(200);
		DECLARE stat_2 VARCHAR(200);
		DECLARE stat_3 VARCHAR(200);
		DECLARE active_2 TIME;
		DECLARE active_3 TIME;
		DECLARE brk_1 INT DEFAULT 0;
		DECLARE nocall_1 INT DEFAULT 0;
		DECLARE meeting_1 INT DEFAULT 0;
		DECLARE calib_1 INT DEFAULT 0;
		DECLARE event_1 INT DEFAULT 0;
		DECLARE med_1 INT DEFAULT 0;
		DECLARE down_1 INT DEFAULT 0;
		DECLARE brk_2 INT DEFAULT 0;
		DECLARE nocall_2 INT DEFAULT 0;
		DECLARE meeting_2 INT DEFAULT 0;
		DECLARE calib_2 INT DEFAULT 0;
		DECLARE event_2 INT DEFAULT 0;
		DECLARE med_2 INT DEFAULT 0;
		DECLARE down_2 INT DEFAULT 0;
		DECLARE idl INT;
		DECLARE act INT;
		DECLARE act1 INT;
		DECLARE idl_1 INT;
		DECLARE totl INT;
		DECLARE u INT;
		DECLARE done BOOLEAN DEFAULT FALSE;
	   DECLARE cur_1 CURSOR FOR SELECT uid,NAME FROM empuid WHERE PROCESS=pro;
	   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	   -- SET dt=DATE_ADD(CURRENT_DATE,INTERVAL -3 DAY);
	   OPEN cur_1;
		my_loop1:LOOP
			FETCH cur_1 INTO uid_1,name_1;
			IF done THEN
			LEAVE my_loop1;
			END IF;
			-- select uid_1,name_1;
			SET tot_active_time_1=0;
			SET tot_idle_time_1=0;
			SET brk_2=0;
			SET nocall_2=0;
			SET meeting_2=0;
			SET calib_2=0;
			SET event_2=0;
			SET down_2=0;
			SET med_2=0;
			SET tot_active_time=0;
			SET tot_idle_time=0;
			SET brk_1=0;
			SET nocall_1=0;
			SET meeting_1=0;
			SET calib_1=0;
			SET event_1=0;
			SET down_1=0;
			SET med_1=0;
			SET shift_start=(SELECT mytime FROM att WHERE username=uid_1 AND tday=dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime >'06:00:00' ORDER BY mytime ASC LIMIT 1);
			IF shift_start IS NOT NULL THEN
 				SET shift_end=(SELECT mytime FROM att WHERE username=uid_1 AND tday=dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='23:59:00' ORDER BY mytime DESC LIMIT 1);
 				block2 : BEGIN
 				DECLARE done2 BOOLEAN DEFAULT FALSE;
 				DECLARE cur_2 CURSOR FOR SELECT mytime,STATUS,active FROM att WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start;
 				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
 				SET first_time = (SELECT MIN(mytime) FROM att WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%'));
 				
 				
					SET tot_active_time=0;
					SET tot_idle_time=0;
					SET brk_1=0;
					SET meeting_1=0;
					SET calib_1=0;
					SET event_1=0;
					SET down_1=0;
					SET med_1=0;
					SET nocall_1=0;
					SET c=0;
					OPEN cur_2;
 					my_loop2:LOOP
 					FETCH cur_2 INTO mytime_2,stat_2,active_2;
 					IF done2 THEN
 					LEAVE my_loop2;
 					END IF;
 					SET next_time=mytime_2;
					SET time_diff=TIMEDIFF(next_time,first_time);
					-- SELECT uid_1,first_time,next_time,time_diff;
					IF stat_2='Idle' THEN
						SET idl=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_idle_time=tot_idle_time+idl;
					ELSEIF stat_2='Active' THEN
						-- SET c=c+1;
						SET act=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_active_time=tot_active_time+act;
					ELSEIF stat_2='END BREAK' THEN
						SET brk_1=brk_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEETING' THEN
						SET meeting_1= meeting_1 + (SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END CALLIBRATION' THEN
						SET calib_1= calib_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END EVENT' THEN
						SET event_1= event_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEDICAL ISSUE' THEN
						SET med_1= med_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END DOWN TIME' THEN
						SET down_1= down_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END NO CALLS' THEN
						SET nocall_1= nocall_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					END IF;
					-- SELECT uid_1,first_time,next_time,time_diff,tot_idle_time;
					SET first_time=next_time;
					
					END LOOP my_loop2;
					-- SELECT tot_idle_time; 
					/*SET tot_idle_time=tot_idle_time-(brk_1+meeting_1+calib_1+event_1+med_1+down_1);
					if (tot_idle_time<0) then
					set tot_idle_time=0;
					end if;*/
					-- SELECT uid_1,shift_start,shift_end,tot_active_time,tot_idle_time,brk_1,meeting_1,calib_1,event_1,med_1,down_1;
				CLOSE cur_2;
				END block2;
				
				-- SET shift_end=DATE_FORMAT(shift_end, '%H:%i:%s');
				IF shift_end>'22:30:00' AND shift_end<'23:59:59' THEN
				
					SET nxt_dt=DATE_ADD(dt,INTERVAL 1 DAY);
					SET shift_start_1=(SELECT mytime AS shift_start FROM att WHERE username=uid_1 AND tday=nxt_dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime>'00:00:01' ORDER BY mytime ASC LIMIT 1);
					IF shift_start_1 IS NOT NULL THEN
					SET shift_end_1=(SELECT mytime AS shift_end FROM att WHERE username=uid_1 AND tday=nxt_dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='06:00:00' ORDER BY mytime DESC LIMIT 1);
					block3 : BEGIN
					DECLARE done3 BOOLEAN DEFAULT FALSE;
					DECLARE cur_3 CURSOR FOR SELECT mytime,STATUS,active FROM att WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1  AND mytime >=shift_start_1;
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done3 = TRUE;
					SET first_time_1 = (SELECT MIN(mytime) FROM att WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1 AND mytime >=shift_start_1);
					SET tot_active_time_1=0;
					SET tot_idle_time_1=0;
					SET brk_2=0;
					SET meeting_2=0;
					SET calib_2=0;
					SET event_2=0;
					SET down_2=0;
					SET med_2=0;
					SET c_1=0;
					SET nocall_2=0;
					OPEN cur_3;
						my_loop3:LOOP
						FETCH cur_3 INTO mytime_3,stat_3,active_3;
						SET next_time_1=mytime_3;
						SET time_diff_1=TIMEDIFF(next_time_1,first_time_1);
						-- SELECT uid_1,first_time_1,next_time_1,time_diff_1;
						IF stat_3='Idle' THEN
							SET idl_1=ROUND(TIME_TO_SEC(time_diff_1)/60,0);
							SET tot_idle_time_1=tot_idle_time_1+idl_1;
						ELSEIF stat_3='Active' THEN
						-- SET c=c+1;
							SET act=(SELECT ROUND(TIME_TO_SEC(time_diff_1)/60,0));
							SET tot_active_time_1=tot_active_time_1+act;
						ELSEIF stat_3='END BREAK' THEN
							SET brk_2=brk_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEETING' THEN
							SET meeting_2=meeting_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END CALLIBRATION' THEN
							SET calib_2=calib_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END EVENT' THEN
							SET event_2=event_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEDICAL ISSUE' THEN
							SET med_2=med_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END DOWN TIME'  THEN
							SET down_2=down_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END NO CALLS'  THEN
							SET nocall_2=nocall_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						END IF;
						SET first_time_1=next_time_1;
						IF done3 THEN
						LEAVE my_loop3;
						END IF;
						END LOOP my_loop3;
						
						/*SET tot_idle_time_1=tot_idle_time_1-(brk_2+meeting_2+calib_2+event_2+med_2+down_2);
						IF (tot_idle_time_1<0) THEN
						SET tot_idle_time_1=0;
						END IF;*/
						-- SET tot_active_time_1=c_1*5;
						-- SELECT uid_1,shift_start_1,shift_end_1,tot_active_time_1,tot_idle_time_1,brk_2,meeting_2,calib_2,event_2,med_2;	
						-- SELECT uid_1,shift_start_1,shift_end_1;
					CLOSE cur_3;
					END block3;
					
					
					END IF;
					
				END IF;
					
					SET tot_break_time=brk_1+brk_2;
					SET tot_meeting_time=meeting_1+meeting_2;
					SET tot_callibration_time=calib_1+calib_2;
					SET tot_event_time=event_1+event_2;
					SET tot_medical_time=med_1+med_2;
					SET tot_down_time=down_1+down_2;
					SET tot_nocall_time=nocall_1+nocall_2;
					SET tot_active_time=tot_active_time+tot_active_time_1;
					SET tot_idle_time=tot_idle_time+tot_idle_time_1;
				SET totl=tot_active_time+tot_idle_time+tot_break_time+tot_meeting_time+tot_callibration_time+tot_event_time+tot_medical_time+tot_down_time+tot_nocall_time;
				
				SET u=(SELECT ROUND((tot_active_time/totl)*100));	
				
				/*SELECT uid_1 as uname,u as util_perc,totl,tot_active_time as totl_active_time,tot_idle_time as totl_idle_time,tot_break_time as break_time,tot_meeting_time as meeting_time,tot_callibration_time as callibration_time,tot_event_time as event_time,tot_medical_time as medical_time,tot_down_time as down_time;*/
				IF u <> 0  THEN
				INSERT INTO util_copy(uname,PROCESS,util_perc,totl,totl_active_time,totl_idle_time,break_time,meeting_time,callibration_time,event_time,medical_time,down_time,nocall_time,DATE) VALUES (name_1,pro,u,totl,tot_active_time,tot_idle_time,tot_break_time,tot_meeting_time,tot_callibration_time,tot_event_time,tot_medical_time,tot_down_time,tot_nocall_time,dt);
				END IF;
 			END IF;
 			
		END LOOP my_loop1;
	   CLOSE cur_1;
	   END$$

DELIMITER ;

------------------------------------------------------------------------------------

"auto_ins_check"
-----------------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `auto_ins_check`$$

CREATE DEFINER=`root`@`%` PROCEDURE `auto_ins_check`(IN dt DATE,IN pro VARCHAR(200))
BEGIN
	
		DECLARE nxt_dt DATE;
		DECLARE shift_start TIME;
		DECLARE shiftstart TIME;
		DECLARE shift_start_1 TIME;
		DECLARE shift_end TIME;
		DECLARE shiftend TIME;
		DECLARE shift_end_1 TIME;
		DECLARE first_time TIME;
		DECLARE first_time_1 TIME;
		DECLARE tot_active_time INT DEFAULT 0;
		DECLARE tot_break_time INT DEFAULT 0;
		DECLARE tot_meeting_time INT DEFAULT 0;
		DECLARE tot_event_time INT DEFAULT 0;
		DECLARE tot_medical_time INT DEFAULT 0;
		DECLARE tot_down_time INT DEFAULT 0;
		DECLARE tot_nocall_time INT DEFAULT 0;
		DECLARE tot_train_time INT DEFAULT 0;
		DECLARE tot_callibration_time INT DEFAULT 0;
		DECLARE tot_active_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time INT DEFAULT 0;
		DECLARE tot_idle_time_1 INT DEFAULT 0;
		DECLARE tot_idle_time_2 INT DEFAULT 0;
		DECLARE c INT DEFAULT 0;
		DECLARE c_1 INT;
		DECLARE mytime_2 TIME;
		DECLARE mytime_3 TIME;
		DECLARE next_time TIME;
		DECLARE next_time_1 TIME;
		DECLARE last_time TIME;
		DECLARE last_time_1 TIME;
		DECLARE time_diff TIME;
		DECLARE time_diff_1 TIME;
		DECLARE uid_1 VARCHAR(200);
		DECLARE ecn_1 VARCHAR(200);
		DECLARE name_1 VARCHAR(200);
		DECLARE stat_2 VARCHAR(200);
		DECLARE chkstat VARCHAR(200);
		DECLARE chkstat1 VARCHAR(200);
		DECLARE stat_3 VARCHAR(200);
		DECLARE active_2 TEXT;
		DECLARE active_3 TEXT;
		DECLARE brk_1 INT DEFAULT 0;
		DECLARE meeting_1 INT DEFAULT 0;
		DECLARE calib_1 INT DEFAULT 0;
		DECLARE event_1 INT DEFAULT 0;
		DECLARE med_1 INT DEFAULT 0;
		DECLARE down_1 INT DEFAULT 0;
		DECLARE nocall_1 INT DEFAULT 0;
		DECLARE train_1 INT DEFAULT 0;
		DECLARE brk_2 INT DEFAULT 0;
		DECLARE meeting_2 INT DEFAULT 0;
		DECLARE calib_2 INT DEFAULT 0;
		DECLARE event_2 INT DEFAULT 0;
		DECLARE med_2 INT DEFAULT 0;
		DECLARE down_2 INT DEFAULT 0;
		DECLARE nocall_2 INT DEFAULT 0;
		DECLARE train_2 INT DEFAULT 0;
		DECLARE idl INT;
		DECLARE act INT;
		DECLARE act1 INT;
		DECLARE idl_1 INT;
		DECLARE totl INT;
		DECLARE u INT;
		DECLARE done BOOLEAN DEFAULT FALSE;
	   DECLARE cur_1 CURSOR FOR SELECT ecn,uid,NAME FROM empuid WHERE PROCESS=pro;
	   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	   -- SET dt=DATE_ADD(CURRENT_DATE,INTERVAL -3 DAY);
	   OPEN cur_1;
		my_loop1:LOOP
			FETCH cur_1 INTO ecn_1,uid_1,name_1;
			IF done THEN
			LEAVE my_loop1;
			END IF;
			SET tot_break_time =0;
			SET tot_meeting_time =0;
			SET tot_event_time =0;
			SET tot_medical_time =0;
			SET tot_down_time =0;
			SET tot_callibration_time =0;
			SET tot_active_time_1=0;
			SET tot_idle_time_1=0;
			SET brk_2=0;
			SET meeting_2=0;
			SET calib_2=0;
			SET event_2=0;
			SET down_2=0;
			SET med_2=0;	
			SET nocall_2=0;
			SET train_2=0;
			SET tot_active_time=0;
			SET tot_idle_time=0;
			SET brk_1=0;
			SET meeting_1=0;
			SET calib_1=0;
			SET event_1=0;
			SET down_1=0;
			SET med_1=0;
			SET nocall_1=0;
			SET train_1=0;
			-- select uid_1,name_1;
			
			SET shift_start=(SELECT mytime FROM att_copy WHERE username=uid_1 AND tday=dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime >'06:00:00' ORDER BY mytime ASC LIMIT 1);
			SET shiftstart=shift_start;
			IF shift_start IS NOT NULL THEN
 				SET shift_end=(SELECT mytime FROM att_copy WHERE username=uid_1 AND tday=dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='23:59:59' ORDER BY mytime DESC LIMIT 1);
 				SET shiftend=shift_end;
 				block2 : BEGIN
 				DECLARE done2 BOOLEAN DEFAULT FALSE;
 				DECLARE cur_2 CURSOR FOR SELECT mytime,STATUS,active FROM att_copy WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start;
 				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
 				SET first_time = (SELECT MIN(mytime) FROM att_copy WHERE tday=dt AND username=uid_1 AND mytime <=shift_end  AND mytime >=shift_start  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%'));
 				
 				
					SET tot_active_time=0;
					SET tot_idle_time=0;
					SET brk_1=0;
					SET meeting_1=0;
					SET calib_1=0;
					SET event_1=0;
					SET down_1=0;
					SET med_1=0;
					SET nocall_1=0;
					SET train_1=0;
					SET c=0;
					OPEN cur_2;
 					my_loop2:LOOP
 					FETCH cur_2 INTO mytime_2,stat_2,active_2;
 					IF done2 THEN
 					LEAVE my_loop2;
 					END IF;
 					
 					SET next_time=mytime_2;
					SET time_diff=TIMEDIFF(next_time,first_time);
					-- SELECT shift_start,shift_end,active_2,uid_1,first_time,next_time,time_diff,stat_2;
					IF stat_2='Idle' THEN
						SET idl=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_idle_time=tot_idle_time+idl;
					ELSEIF stat_2='Active' THEN
						-- SET c=c+1;
						SET act=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_active_time=tot_active_time+act;
					ELSEIF stat_2='START BREAK' OR stat_2='START MEETING' OR stat_2='START CALLIBRATION' OR stat_2='START EVENT' OR stat_2='START MEDICAL ISSUE' OR stat_2='START NO CALLS' OR stat_2='START DOWN TIME' OR stat_2='START TRAINING' THEN
						
						SET chkstat=(SELECT STATUS FROM att_copy WHERE tday=dt AND username=uid_1 AND mytime<mytime_2 ORDER BY mytime DESC LIMIT 1);
						IF chkstat='Active' OR chkstat='END BREAK' OR chkstat='END MEETING' OR chkstat='END CALLIBRATION' OR chkstat='END EVENT' OR 'END MEDICAL ISSUE' OR chkstat='END NO CALLS' OR chkstat='END DOWN TIME' OR chkstat='END TRAINING' THEN
						SET act=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_active_time=tot_active_time+act;
						ELSEIF chkstat='Idle' THEN
						SET idl=(SELECT ROUND(TIME_TO_SEC(time_diff)/60,0));
						SET tot_idle_time=tot_idle_time+idl;
						END IF;
						
					ELSEIF stat_2='END BREAK' THEN
						SET brk_1=brk_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEETING' THEN
						SET meeting_1= meeting_1 + (SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END CALLIBRATION' THEN
						SET calib_1= calib_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END EVENT' THEN
						SET event_1= event_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END MEDICAL ISSUE' THEN
						SET med_1= med_1+(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END DOWN TIME' THEN
						SET down_1= down_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END NO CALLS' THEN
						SET nocall_1= nocall_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					ELSEIF stat_2='END TRAINING' THEN
						SET train_1= train_1 +(SELECT ROUND(TIME_TO_SEC(active_2)/60,0));
					END IF;
					SET first_time=next_time;
					
					END LOOP my_loop2;
					-- SELECT tot_idle_time; 
					/*SET tot_idle_time=tot_idle_time-(brk_1+meeting_1+calib_1+event_1+med_1+down_1);
					if (tot_idle_time<0) then
					set tot_idle_time=0;
					end if;*/
					
					-- SELECT uid_1,shift_start,shift_end,tot_active_time,tot_idle_time,brk_1,meeting_1,calib_1,event_1,med_1,down_1;
				CLOSE cur_2;
				END block2;
				
				-- SET shift_end=DATE_FORMAT(shift_end, '%H:%i:%s');
				IF shift_end>'19:30:00' AND shift_end<'23:59:59' THEN
				
					SET nxt_dt=DATE_ADD(dt,INTERVAL 1 DAY);
					SET shift_start_1=(SELECT mytime AS shift_start FROM att_copy WHERE username=uid_1 AND tday=nxt_dt AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime>='00:00:00' ORDER BY mytime ASC LIMIT 1);
					IF shift_start_1 IS NOT NULL THEN
					SET shift_end_1=(SELECT mytime AS shift_end FROM att_copy WHERE username=uid_1 AND tday=nxt_dt  AND (STATUS='Active' OR STATUS LIKE 'START%' OR STATUS LIKE 'END%') AND mytime<='06:00:00' ORDER BY mytime DESC LIMIT 1);
					SET shiftend=shift_end_1;
					block3 : BEGIN
					DECLARE done3 BOOLEAN DEFAULT FALSE;
					DECLARE cur_3 CURSOR FOR SELECT mytime,STATUS,active FROM att_copy WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1  AND mytime >=shift_start_1;
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done3 = TRUE;
					SET first_time_1 = (SELECT MIN(mytime) FROM att_copy WHERE tday=nxt_dt AND username=uid_1 AND mytime <=shift_end_1 AND mytime >=shift_start_1);
					SET tot_active_time_1=0;
					SET tot_idle_time_1=0;
					SET brk_2=0;
					SET meeting_2=0;
					SET calib_2=0;
					SET event_2=0;
					SET down_2=0;
					SET med_2=0;
					SET nocall_2=0;
					SET train_2=0;
					SET c_1=0;
					OPEN cur_3;
						my_loop3:LOOP
						FETCH cur_3 INTO mytime_3,stat_3,active_3;
						IF done3 THEN
						LEAVE my_loop3;
						END IF;
						SET next_time_1=mytime_3;
						SET time_diff_1=TIMEDIFF(next_time_1,first_time_1);
						-- SELECT stat_3,mytime_3,active_3;
						IF stat_3='Idle' THEN
							SET idl_1=ROUND(TIME_TO_SEC(time_diff_1)/60,0);
							SET tot_idle_time_1=tot_idle_time_1+idl_1;
						ELSEIF stat_3='Active' THEN
						
						-- SET c=c+1;
							SET act=(SELECT ROUND(TIME_TO_SEC(time_diff_1)/60,0));
							SET tot_active_time_1=tot_active_time_1+act;
						ELSEIF stat_3='START BREAK' OR stat_3='START MEETING' OR stat_3='START CALLIBRATION' OR stat_3='START EVENT' OR stat_3='START MEDICAL ISSUE' OR stat_3='START NO CALLS' OR stat_3='START DOWN TIME' OR stat_3='START TRAINING' THEN
						
							SET chkstat1=(SELECT STATUS FROM att_copy WHERE tday=nxt_dt AND username=uid_1 AND mytime<mytime_3 ORDER BY mytime DESC LIMIT 1);
							IF chkstat1='Active' OR chkstat1='END BREAK' OR chkstat1='END MEETING' OR chkstat1='END CALLIBRATION' OR chkstat1='END EVENT' OR chkstat1='END MEDICAL ISSUE' OR chkstat1='END NO CALLS' OR chkstat1='END DOWN TIME' OR chkstat1='END TRAINING' THEN
							SET act=(SELECT ROUND(TIME_TO_SEC(time_diff_1)/60,0));
							SET tot_active_time_1=tot_active_time_1+act;
							ELSEIF chkstat1='Idle' THEN
							SET idl_1=ROUND(TIME_TO_SEC(time_diff_1)/60,0);
							SET tot_idle_time_1=tot_idle_time_1+idl_1;
							END IF;
						ELSEIF stat_3='END BREAK' THEN
							SET brk_2=brk_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEETING' THEN
							SET meeting_2=meeting_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END CALLIBRATION' THEN
							SET calib_2=calib_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END EVENT' THEN
						-- SELECT mytime_3,stat_3,active_3;
							SET event_2=event_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END MEDICAL ISSUE' THEN
							SET med_2=med_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END DOWN TIME'  THEN
							SET down_2=down_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END NO CALLS'  THEN
							SET nocall_2=nocall_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						ELSEIF stat_3='END TRAINING'  THEN
							SET train_2=train_2+(SELECT ROUND(TIME_TO_SEC(active_3)/60,0));
						END IF;
						SET first_time_1=next_time_1;
						
						END LOOP my_loop3;
						
						/*SET tot_idle_time_1=tot_idle_time_1-(brk_2+meeting_2+calib_2+event_2+med_2+down_2);
						IF (tot_idle_time_1<0) THEN
						SET tot_idle_time_1=0;
						END IF;*/
						-- SET tot_active_time_1=c_1*5;
						-- SELECT uid_1,shift_start_1,shift_end_1,tot_active_time_1,tot_idle_time_1,brk_2,meeting_2,calib_2,event_2,med_2,nocall_2;	
						
					CLOSE cur_3;
					END block3;
					
					
					END IF;
					
				END IF;
					
					SET tot_break_time=tot_break_time+brk_1+brk_2;
					SET tot_meeting_time=meeting_1+meeting_2;
					SET tot_callibration_time=calib_1+calib_2;
					SET tot_event_time=event_1+event_2;
					SET tot_medical_time=med_1+med_2;
					SET tot_down_time=down_1+down_2;
					SET tot_nocall_time=nocall_1+nocall_2;
					SET tot_train_time=train_1+train_2;
					SET tot_active_time=tot_active_time+tot_active_time_1;
					SET tot_idle_time=tot_idle_time+tot_idle_time_1;
				SET totl=tot_active_time+tot_idle_time+tot_break_time+tot_meeting_time+tot_callibration_time+tot_event_time+tot_medical_time+tot_down_time+tot_nocall_time+tot_train_time;
				
				SET u=(SELECT ROUND(((tot_active_time+tot_meeting_time+tot_callibration_time+tot_event_time+tot_medical_time+tot_nocall_time+tot_train_time)/totl)*100));	
				
				-- SELECT uid_1,u,totl,tot_active_time,tot_idle_time,tot_break_time,tot_meeting_time,tot_callibration_time,tot_event_time,tot_medical_time,tot_down_time,tot_nocall_time;
				-- select uid_1,u,shiftstart,shiftend,dt;
				IF u <> 0  THEN
			        INSERT INTO util(ecn,uname,PROCESS,util_perc,totl,totl_active_time,totl_idle_time,break_time,meeting_time,callibration_time,event_time,medical_time,down_time,nocall_time,train_time,shift_start,shift_end,DATE) VALUES (ecn_1,name_1,pro,u,totl,tot_active_time,tot_idle_time,tot_break_time,tot_meeting_time,tot_callibration_time,tot_event_time,tot_medical_time,tot_down_time,tot_nocall_time,tot_train_time,shiftstart,shiftend,dt);
			        END IF;
 			END IF;
 			
		END LOOP my_loop1;
	   CLOSE cur_1;
	   END$$

DELIMITER;
----------------------------------------------------------------------------------------
"curstat"
---------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `curstat`$$

CREATE DEFINER=`root`@`%` PROCEDURE `curstat`(IN mydate DATE, IN pro1 VARCHAR(200))
BEGIN
    DECLARE uname VARCHAR(200);
    DECLARE lastonline TIME;
    DECLARE active1 VARCHAR(250);
    DECLARE tdiffer TIME;
    DECLARE chk INT;
    DECLARE done INT DEFAULT 0;
    DECLARE cur_a CURSOR FOR SELECT DISTINCT(username) FROM att WHERE tday= mydate AND STATUS = 'Áctive' AND username IN (SELECT uid FROM empuid WHERE PROCESS = pro1);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	OPEN cur_a;
	DELETE FROM curstat WHERE pro = pro1;
	my_loop1:LOOP
	FETCH cur_a INTO uname;
	IF done = 1 THEN
		LEAVE my_loop1;	
	ELSE
		SET lastonline = (SELECT DISTINCT(MAX(mytime)) FROM att WHERE username = uname AND tday = mydate AND STATUS = 'Active');
		SET active1 = (SELECT DISTINCT(active) FROM att WHERE username = uname AND mytime = lastonline AND tday = mydate);
		SET tdiffer = TIMEDIFF(CURTIME(),lastonline);
		SET chk = (SELECT COUNT(username) FROM curstat WHERE pro = pro1 AND tdate = mydate AND username = uname);
		IF chk >0 THEN
			UPDATE curstat  SET active = active1,tdiff = tdiffer WHERE pro = pro1 AND tdate = mydate;
		ELSE
			INSERT INTO curstat (username,active,tdiff,pro,tdate) VALUES(uname,active1,tdiffer,pro1,mydate);
		END IF;
		
	END IF;
		
	
	END LOOP my_loop1;
	SELECT * FROM curstat WHERE pro = pro1 AND tdate = mydate;
	CLOSE cur_a;
    END$$

DELIMITER ;
----------------------------------------------------------------------------------------
"Top24"
-------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `Top24`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Top24`(IN uname VARCHAR(200))
BEGIN
    DECLARE cnt INT;
    DECLARE st VARCHAR(20); 
    DECLARE n INT;
    DECLARE current_streak INT;   
	DECLARE cur_a CURSOR  FOR SELECT `status` FROM att WHERE username = uname LIMIT 24;
	SET n = 0;
    SET current_streak=0;	
	OPEN cur_a;		
		my_loop:LOOP
			FETCH cur_a INTO st;	
			IF st = 'Idle' THEN
				SET n= n + 1;
				SET current_streak = current_streak +1;
			ELSE
				LEAVE my_loop;
			END IF;
		END LOOP my_loop;
		SET cnt = n;
		SELECT cnt;	
	CLOSE cur_a;
    END$$

DELIMITER;
----------------------------------------------------------------------------------------
"hope3"
-------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `hope3`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hope3`(IN mydate DATE, IN pro VARCHAR(200))
BEGIN
DECLARE uname VARCHAR(200);
DECLARE comp VARCHAR(100);
DECLARE done INT DEFAULT 0;
DECLARE chkusr INT;
DECLARE shared INT;
	DECLARE cur_a CURSOR FOR SELECT DISTINCT(username),compname FROM att WHERE username IN (SELECT uid FROM empuid WHERE PROCESS = pro) AND tday = mydate;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;	
OPEN cur_a;
	my_loop2:LOOP
	
		FETCH cur_a INTO uname,comp;
		
		SET shared = (SELECT COUNT(DISTINCT(username)) FROM att WHERE compname = comp AND tday = mydate);
		
		SET chkusr = (SELECT SUM(usrcnt) FROM temp4 WHERE PROCESS = pro AND tdate = mydate AND compname = comp) ;
		IF chkusr IS NULL THEN
			IF done = 1 THEN
				LEAVE my_loop2;
			END IF;
			INSERT INTO temp4 (compname,PROCESS,usrcnt,tdate,username)VALUES(comp,pro,shared,mydate,uname);
		ELSE
			IF done = 1 THEN
				LEAVE my_loop2;
			END IF;
			IF shared > chkusr THEN
				UPDATE temp4 SET usrcnt = shared WHERE compname = comp AND PROCESS = pro AND tdate = mydate;
			END IF;
		END IF;
		
		
		
	END LOOP my_loop2;
SELECT * FROM temp4 WHERE tdate = mydate AND PROCESS = pro ;
CLOSE cur_a;
END$$

DELIMITER;
DELIMITER $$
----------------------------------------------------------------------------------------
"hope2"
-------
USE `attendance`$$

DROP PROCEDURE IF EXISTS `hope2`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hope2`(IN mydate DATE)
BEGIN
DECLARE username1 VARCHAR(255);
DECLARE activetime TIME;
DECLARE fivemin INT;
DECLARE shiftstart TIME;
DECLARE shiftend TIME;
DECLARE logintime TIME;
DECLARE logouttime TIME;

DECLARE timecheck INT;

DECLARE timecheck1 TIME;

DECLARE timecheck2 TIME;

DECLARE totalshift INT;
DECLARE onsystime INT;
DECLARE util DECIMAL(11,2);
DECLARE pcnt INT;
DECLARE ocnt INT;
DECLARE timedifference TIME;
DECLARE timeout INT;
DECLARE checkusername VARCHAR(255);
DECLARE datacheck INT;
DECLARE tcheck TIME;
DECLARE namecheck VARCHAR(250);
DECLARE done INT DEFAULT 0;
DECLARE crap INT;
DECLARE cur_2 CURSOR FOR SELECT uname,atime,fivecnt,shift_start,shift_end FROM temp2 WHERE ndate = mydate  ORDER BY uname,atime ;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
OPEN cur_2;
SET ocnt = 0;
SET timecheck = '00:00:00';
SET namecheck = "";
	my_loop2:LOOP
		FETCH cur_2 INTO username1,activetime,fivemin,shiftstart,shiftend;		
		SET ocnt = ocnt+1;	
		
				
				SET namecheck = username1;
				SET tcheck = activetime;
				
				
		
				IF ocnt = 1 THEN
					SET timecheck = fivemin;
					SET timecheck1 = activetime;
					IF timecheck1 IS NULL THEN
						SET timecheck1 = '00:00:00';
					END IF;
					SET timedifference = 0;
					SET timeout  = 0;
					SET checkusername = username1;
					SET logintime = (SELECT shift_start FROM temp2 WHERE NOT shift_start = '00:00:00' AND uname = username1 AND ndate = mydate);
					SET logouttime = (SELECT shift_end FROM temp2 WHERE NOT shift_end = '00:00:00' AND uname = username1 AND ndate = mydate);
					IF logouttime IS NULL THEN
						SET logouttime = '00:00:00';
					END IF;
		
				ELSE
		
					IF checkusername != username1 THEN
						
						SET totalshift = ROUND(((TIME_TO_SEC(timecheck2) - TIME_TO_SEC(logintime)))/60,0);
						SET onsystime = totalshift - timeout ;
						SET util = (onsystime/450)*100;
						
						SET datacheck = (SELECT COUNT(uname) FROM temp3 WHERE tdate = mydate AND uname = checkusername);
                        
                        IF logintime IS NULL THEN
							SET crap = 7;
                        ELSE
							IF datacheck > 0 THEN
								UPDATE temp3 SET uname = checkusername, logout = logouttime, logint = logintime, totaltime = totalshift, offtime = timeout,ontime = onsystime, utilization = util,tdate = mydate,lastsceen = timecheck2 WHERE tdate = mydate AND uname = checkusername;
							ELSE
								INSERT INTO temp3 (uname,logout,logint,totaltime,offtime,ontime,utilization,tdate,lastsceen)VALUES(checkusername,logouttime,logintime,totalshift,timeout,onsystime,util,mydate,timecheck2);
							END IF;
                        END IF;
						
						
						SET timecheck = fivemin;
						SET timedifference = 0;
						SET timeout  = 0;
						SET checkusername = username1;
						SET ocnt = 0;
						SET logintime = (SELECT shift_start FROM temp2 WHERE NOT shift_start = '00:00:00' AND uname = username1 AND ndate = mydate);
						SET logouttime = (SELECT shift_end FROM temp2 WHERE NOT shift_end = '00:00:00' AND uname = username1 AND ndate = mydate);
						IF logouttime IS NULL THEN
							SET logouttime = '00:00:00';
						END IF;
						IF logouttime = '00:00:00' THEN
							IF activetime >= logintime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;	
						ELSEIF logouttime < logintime THEN
							IF logouttime >= activetime >= logintime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;
						ELSEIF logintime < logouttime THEN
							IF logintime <= activetime <= logouttime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;
							END IF;
							END IF;
							END IF;
							END IF;
		
					ELSE
						IF logouttime = '00:00:00' THEN
							IF activetime >= logintime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;	
						ELSEIF logouttime < logintime THEN
							IF logouttime >= activetime >= logintime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;
						ELSEIF logintime < logouttime THEN
							IF logintime <= activetime <= logouttime THEN
								SET timedifference = TIMEDIFF(activetime,timecheck1);
								SET timecheck1 = activetime;
								IF timedifference > '00:05:50' THEN
									SET timeout = timeout + ROUND((TIME_TO_SEC(timedifference)/60),0);
								END IF;
							END IF;		
						END IF;
						END IF;
						END IF;
					END IF;			
				END IF;			
				
				SET timecheck 	= fivemin;
				SET timecheck2 	= activetime;
					IF done = 1 THEN
						SET totalshift = ROUND(((TIME_TO_SEC(timecheck2) - TIME_TO_SEC(logintime)))/60,0);
						SET onsystime = totalshift - timeout ;
						SET util = (onsystime/450)*100;
						SET datacheck = (SELECT COUNT(uname) FROM temp3 WHERE tdate = mydate AND uname = checkusername);
                        IF logintime IS NULL  THEN
							SET crap = 7;
                        ELSE
                        IF datacheck > 0 THEN
								UPDATE temp3 SET uname = checkusername, logout = logouttime, logint = logintime, totaltime = totalshift, offtime = timeout,ontime = onsystime, utilization = util,tdate = mydate,lastsceen = timecheck2 WHERE tdate = mydate AND uname = checkusername;
							ELSE
								INSERT INTO temp3 (uname,logout,logint,totaltime,offtime,ontime,utilization,tdate,lastsceen)VALUES(checkusername,logouttime,logintime,totalshift,timeout,onsystime,util,mydate,timecheck2);
							END IF;
                        END IF;
							
						
						LEAVE my_loop2;	
					END IF;					
		
		
END LOOP my_loop2;
SELECT * FROM temp3 WHERE tdate = mydate;
CLOSE cur_2;
END$$

DELIMITER ;
------------------------------------------------------------------------------------------
"hope"
------
DELIMITER $$

USE `attendance`$$

DROP PROCEDURE IF EXISTS `hope`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hope`(IN mydate DATE)
BEGIN
DECLARE username1 VARCHAR(250);
DECLARE activetime1 TIME;
DECLARE done INT DEFAULT 0;
DECLARE fivepoint INT;
DECLARE checkfive INT;
DECLARE checktime TIME;
DECLARE shftstart TIME;
DECLARE shftend TIME;
DECLARE checkshift INT;
DECLARE timediffrence TIME;
DECLARE wind INT;
DECLARE crap INT;
DECLARE active1 VARCHAR(250);
DECLARE cur_1 CURSOR FOR SELECT username,mytime,active FROM att  WHERE tday = mydate AND STATUS = 'Active' ORDER BY mytime ASC;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
DELETE FROM temp2 WHERE ndate = DATE_ADD(mydate,INTERVAL -32 DAY);
OPEN cur_1;
	my_loop1:LOOP
	FETCH cur_1 INTO username1,activetime1,active1;
	
	SET fivepoint = (SELECT ROUND(TIME_TO_SEC(activetime1)/300,0));
	
	SET checkfive = (SELECT COUNT(fivecnt) FROM temp2 WHERE ndate = mydate AND uname = username1 AND atime = activetime1);
	
	SET checktime = (SELECT MAX(atime) FROM temp2 WHERE uname = username1 AND ndate = mydate);
	SET checkshift= (SELECT COUNT(shift_start) FROM temp2 WHERE NOT shift_start='00:00:00' AND ndate=mydate AND uname=username1);
    
	
    IF active1 = "" THEN
    SET crap = 9;
    ELSE
		IF checkfive = 0 THEN
			IF checktime IS NOT NULL THEN
				SET timediffrence = TIMEDIFF(activetime1,checktime);
				
				IF timediffrence >= '04:00:00' THEN
					IF checkshift>0 THEN
						SET crap=100;
					ELSE
						SET shftstart = activetime1;
					
						UPDATE temp2 SET shift_end = checktime WHERE uname = username1 AND atime = checktime AND ndate = mydate;
					END IF;
				END IF;
			ELSE
				SET timediffrence = '00:00:00';
				
				IF fivepoint > 48 THEN
					IF checkshift>0 THEN
						SET crap=100;
					ELSE
					
						SET shftstart = activetime1;
					END IF;
				END IF;
				
				IF timediffrence >= '04:00:00' THEN
					IF checkshift>0 THEN
						SET crap=100;
					ELSE
					
						SET shftstart = activetime1;
				
						UPDATE temp2 SET shift_end = checktime WHERE uname = username1 AND atime = checktime AND ndate = mydate;
					END IF;
				END IF;
			END IF;
			
			INSERT INTO temp2(uname,atime,fivecnt,shift_start,shift_end,ndate)VALUES(username1,activetime1,fivepoint,shftstart,'',mydate);
		END IF;
    END IF;
	
	IF done = 1 THEN
			LEAVE my_loop1;	
	END IF;
	SET shftstart = "";
	SET shftend = "";
	END LOOP my_loop1;
	SELECT * FROM temp2 WHERE ndate = mydate;
CLOSE cur_1;
END$$

DELIMITER ;
--------------------------------------------------------------------------------------------------------------