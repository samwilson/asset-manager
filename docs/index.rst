AssetManager
============

AssetManager is a web-based database system for managing fixed assets and equipment,
along with work that needs to be performed on the assets and who is to perform it.

* Website: https://github.com/samwilson/asset-manager
* Issue tracker: https://github.com/samwilson/asset-manager/issues
* Licence: GPL v3

Definitions
-----------

An **Asset** is any physical piece of infrastructure, equipment, or machinery.
It has a unique alphanumeric identifier, and a fixed location.

A **Job** is an instance of work that is required to be performed on an Asset.

A **Job Type** is the definition of what variety of work is to be performed.

A **User** is any person who uses AssetManager.
Access is controlled by Users' membership of Groups
(the defaults being Administrators, Managers, and Clerks).

A **Job List** is a collection of Jobs, all of the same Job Type,
and is the unity by which Jobs are scheduled to Crews.

A **Crew** is a group of one or more Users,
and is the unit to which Job Lists are assigned.

Table of Contents
-----------------

.. toctree::
   :maxdepth: 2
   :numbered:

   assets
