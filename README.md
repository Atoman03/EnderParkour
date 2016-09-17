# EnderParkour
An Advanced Parkour Plugin for Genisys and other Softwares! A plugin for Pocket_Servers

_**Sections**_

_[Status](https://github.com/CaptainDuck/EnderParkour#status)_

_[Features](https://github.com/CaptainDuck/EnderParkour#features)_

_[Config File](https://github.com/CaptainDuck/EnderParkour#config)_

_[Ideas/Being worked on](https://github.com/CaptainDuck/EnderParkour#ideas)_

##Status
**Done! Tested!**

##Features
__1. Checkpoints! Just create a sign with a text on the 1st Line! (Text editable in config)__

__2. When players finish the parkour (By tapping a sign with a text, text editable in config), the player can send and the console can also send a command! (Editable by config)__

__3. When a player falls into a void (World/level editable in config), the player will teleport to his/her's last checkpoint! (Editable in config)__

__4. Custom Finish and Checkpoint messages! (Editable in config)__

__5. Super easy to use and User Interface! :P (UI)__

##Config
`````
#EnderParkour Config

#Checkpoints Stuff
CheckpointTextSign: "[Checkpoint]"
CheckpointMsg: "Checkpoint set!"

#Finish Parkour Stuff
FinishTextSign: "[Finish]"
FinishMsg: "You have finished teh parkour!"
FinishPlayerCmd: "spawn"
FinishConsoleCmd: "say {PLAYER} finished parkour!"

#Void Stuff
TeleportToLastCheckpointOnVoid: "true"
NoVoidWorld: "world"
#When Player has no Checkpoint
VoidPlayerCmd: "hub"
`````

##Ideas

***Create a function/command that lets people create a Parkour world that will handle all plugin's features.**

***Parkour Countdown.**

***Deletes the data/parkour checkpoints upon parkour finish.**

_**More will be added soon, stay tuned! :D**_

_**These ideas are being worked and are planned for future updates :D, You could see the progress at this [Branch](https://github.com/CaptainDuck/EnderParkour/tree/V1.1.0).**_
